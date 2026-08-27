<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\PerencanaanPembelian;
use App\Models\DetailPerencanaanPembelian;
use App\Models\VarianBarang;
use App\Models\StokLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class PenerimaanPembelianController extends Controller
{
    public function index()
    {
        $perencanaan = PerencanaanPembelian::with([
            'supplier',
            'details.varian.barang'
        ])
            ->whereNotIn('status', [
                'selesai',
                'dibatalkan'
            ])
            ->whereHas('details', function ($query) {
                $query->whereColumn(
                    'qty_diterima',
                    '<',
                    'qty_rencana'
                );
            })
            ->latest()
            ->get();

        return view(
            'penerimaan-pembelian.index',
            compact('perencanaan')
        );
    }
    public function create(
        PerencanaanPembelian $perencanaanPembelian
    ) {

        if (
            in_array(
                $perencanaanPembelian->status,
                [
                    'selesai',
                    'dibatalkan',
                ]
            )
        ) {
            return redirect()
                ->route('perencanaan-pembelian.show', $perencanaanPembelian)
                ->with(
                    'error',
                    'Perencanaan ini sudah selesai atau dibatalkan.'
                );
        }

        $perencanaanPembelian->load([
            'supplier',
            'details' => function ($query) {

                $query->whereColumn(
                    'qty_diterima',
                    '<',
                    'qty_rencana'
                );

            },

            'details.varian.barang',
        ]);

        if ($perencanaanPembelian->details->isEmpty()) {

            return redirect()
                ->route(
                    'perencanaan-pembelian.show',
                    $perencanaanPembelian
                )
                ->with(
                    'error',
                    'Tidak ada barang yang tersisa untuk diterima.'
                );
        }

        return view(
            'penerimaan-pembelian.create',
            [
                'perencanaan' =>
                    $perencanaanPembelian
            ]
        );
    }

    public function store(
        Request $request,
        PerencanaanPembelian $perencanaanPembelian
    ) {

        $request->validate([
            'no_faktur' => [
                'required',
                'string',
                'max:255',
                'unique:pembelian,no_faktur',
            ],

            'tanggal_pembelian' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.detail_perencanaan_id' => [
                'required',
                'exists:detail_perencanaan_pembelian,id',
            ],

            'items.*.qty_diterima' => [
                'required',
                'integer',
                'min:0',
            ],

            'items.*.harga_satuan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.diskon' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        if (
            in_array(
                $perencanaanPembelian->status,
                [
                    'selesai',
                    'dibatalkan',
                ]
            )
        ) {
            return back()->with(
                'error',
                'Perencanaan ini tidak dapat menerima barang lagi.'
            );
        }

        $marketplace = app(MarketplaceController::class);

        try {

            DB::transaction(function () use ($request, $perencanaanPembelian, $marketplace) {

                $perencanaan =
                    PerencanaanPembelian::where(
                        'id',
                        $perencanaanPembelian->id
                    )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    in_array(
                        $perencanaan->status,
                        [
                            'selesai',
                            'dibatalkan',
                        ]
                    )
                ) {

                    throw ValidationException::withMessages([
                        'penerimaan' =>
                            'Perencanaan sudah selesai atau dibatalkan.'
                    ]);
                }

                $itemsDiterima = collect(
                    $request->items
                )->filter(function ($item) {

                    return
                        (int) ($item['qty_diterima'] ?? 0)
                        > 0;

                });

                if ($itemsDiterima->isEmpty()) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Masukkan minimal satu barang yang diterima.'
                    ]);
                }

                $pembelian = Pembelian::create([

                    'perencanaan_pembelian_id' =>
                        $perencanaan->id,

                    'no_faktur' =>
                        $request->no_faktur,

                    'tanggal_pembelian' =>
                        $request->tanggal_pembelian,

                    'supplier_id' =>
                        $perencanaan->supplier_id,

                    'status' =>
                        'diterima',

                    'user_id' =>
                        auth()->id(),

                    'total_brutto' =>
                        0,

                    'total_diskon' =>
                        0,

                    'total_netto' =>
                        0,
                ]);

                $totalBrutto = 0;
                $totalDiskon = 0;

                foreach ($itemsDiterima as $item) {

                    $detailRencana =
                        DetailPerencanaanPembelian::where(
                            'id',
                            $item['detail_perencanaan_id']
                        )
                            ->where(
                                'perencanaan_pembelian_id',
                                $perencanaan->id
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$detailRencana) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Detail perencanaan tidak valid.'
                        ]);
                    }

                    $qtyMasuk =
                        (int) $item['qty_diterima'];

                    $sisa =
                        $detailRencana->qty_rencana
                        -
                        $detailRencana->qty_diterima;

                    if ($qtyMasuk > $sisa) {

                        throw ValidationException::withMessages([

                            'items' =>
                                'Qty penerimaan melebihi sisa perencanaan.'

                        ]);
                    }

                    $hargaSatuan =
                        (float) (
                            $item['harga_satuan']
                            ??
                            $detailRencana->estimasi_harga
                            ??
                            0
                        );

                    $diskon =
                        (float) (
                            $item['diskon']
                            ??
                            0
                        );

                    $subtotal =
                        $qtyMasuk
                        *
                        $hargaSatuan;

                    if ($diskon > $subtotal) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Diskon tidak boleh melebihi subtotal barang.'
                        ]);
                    }


                    DetailPembelian::create([

                        'pembelian_id' =>
                            $pembelian->id,

                        'varian_id' =>
                            $detailRencana->varian_id,

                        'qty' =>
                            $qtyMasuk,

                        'harga_satuan' =>
                            $hargaSatuan,

                        'diskon' =>
                            $diskon,

                        'subtotal' =>
                            $subtotal,
                    ]);

                    $varian =
                        VarianBarang::where(
                            'id',
                            $detailRencana->varian_id
                        )
                            ->lockForUpdate()
                            ->firstOrFail();

                    $stokSebelum =
                        $varian->stok;

                    $stokSesudah =
                        $stokSebelum
                        +
                        $qtyMasuk;

                    $hargaJual = round(
                        $hargaSatuan + ($hargaSatuan * ((float) $varian->margin_persen) / 100)
                    );

                    $varian->update([

                        'stok' =>
                            $stokSesudah,

                        'harga_beli' =>
                            $hargaSatuan,

                        'harga_jual' =>
                            $hargaJual,

                    ]);

                    // Untuk varian yang termapping, stok Shopee wajib berhasil
                    // diperbarui sebelum penerimaan lokal di-commit.
                    $marketplace->syncSingleStockToShopee($varian);

                    StokLog::create([

                        'varian_id' =>
                            $varian->id,

                        'tipe_transaksi' =>
                            'pembelian',

                        'qty' =>
                            $qtyMasuk,

                        'stok_sebelum' =>
                            $stokSebelum,

                        'stok_sesudah' =>
                            $stokSesudah,

                        'referensi' =>
                            $pembelian->no_faktur,

                    ]);

                    $detailRencana->increment(
                        'qty_diterima',
                        $qtyMasuk
                    );

                    $totalBrutto +=
                        $subtotal;

                    $totalDiskon +=
                        $diskon;

                }

                $pembelian->update([

                    'total_brutto' =>
                        $totalBrutto,

                    'total_diskon' =>
                        $totalDiskon,

                    'total_netto' =>
                        $totalBrutto
                        -
                        $totalDiskon,

                ]);

                $masihAdaSisa =
                    DetailPerencanaanPembelian::where(
                        'perencanaan_pembelian_id',
                        $perencanaan->id
                    )
                        ->whereColumn(
                            'qty_diterima',
                            '<',
                            'qty_rencana'
                        )
                        ->exists();

                $perencanaan->update([

                    'status' =>
                        $masihAdaSisa
                        ? 'sebagian_diterima'
                        : 'selesai'

                ]);
            });

        } catch (ValidationException $e) {

            throw $e;

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Penerimaan gagal diproses: '
                    . $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'perencanaan-pembelian.show',
                $perencanaanPembelian
            )
            ->with(
                'success',
                'Penerimaan barang berhasil disimpan dan stok telah diperbarui.'
            );
    }

    public function show(Pembelian $pembelian)
    {
        if (!$pembelian->perencanaan_pembelian_id) {

            return redirect()
                ->route('penerimaan-pembelian.index')
                ->with(
                    'error',
                    'Data ini bukan transaksi penerimaan dari perencanaan pembelian.'
                );
        }

        $pembelian->load([
            'supplier',
            'user',
            'perencanaan',
            'detailPembelian.varian.barang',
        ]);

        return view(
            'penerimaan-pembelian.show',
            compact('pembelian')
        );
    }
}
