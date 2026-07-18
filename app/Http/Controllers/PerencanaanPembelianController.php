<?php

namespace App\Http\Controllers;

use App\Models\PerencanaanPembelian;
use App\Models\DetailPerencanaanPembelian;
use App\Models\Supplier;
use App\Models\VarianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerencanaanPembelianController extends Controller
{
    /**
     * Menampilkan daftar perencanaan pembelian.
     */
    public function index()
    {
        $perencanaan = PerencanaanPembelian::with([
            'supplier',
            'user',
            'details.varian.barang'
        ])
            ->latest()
            ->get();

        return view(
            'perencanaan-pembelian.index',
            compact('perencanaan')
        );
    }

    /**
     * Menampilkan halaman membuat perencanaan pembelian.
     */
    public function create()
    {
        $supplier = Supplier::orderBy('nama_supplier')
            ->get();

        $varian = VarianBarang::with([
            'barang.kategori'
        ])
            ->orderBy('id')
            ->get();

        return view(
            'perencanaan-pembelian.create',
            compact(
                'supplier',
                'varian'
            )
        );
    }

    /**
     * Menyimpan perencanaan pembelian.
     *
     * CATATAN:
     * Pada tahap ini stok TIDAK bertambah.
     * Stok baru bertambah ketika barang benar-benar diterima.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_perencanaan' => [
                'required',
                'date',
            ],

            'supplier_id' => [
                'required',
                'exists:supplier,id',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'cart' => [
                'required',
                'array',
                'min:1',
            ],

            'cart.*.varian_id' => [
                'required',
                'exists:varian_barang,id',
            ],

            'cart.*.qty' => [
                'required',
                'integer',
                'min:1',
            ],

            'cart.*.estimasi_harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Buat Header Perencanaan
            |--------------------------------------------------------------------------
            */

            $perencanaan = PerencanaanPembelian::create([
                'no_perencanaan' =>
                    'PP-' . now()->format('YmdHis'),

                'tanggal_perencanaan' =>
                    $request->tanggal_perencanaan,

                'supplier_id' =>
                    $request->supplier_id,

                'status' =>
                    'draft',

                'catatan' =>
                    $request->catatan,

                'user_id' =>
                    auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan Detail Perencanaan
            |--------------------------------------------------------------------------
            */

            foreach ($request->cart as $item) {

                DetailPerencanaanPembelian::create([
                    'perencanaan_pembelian_id' =>
                        $perencanaan->id,

                    'varian_id' =>
                        $item['varian_id'],

                    'qty_rencana' =>
                        $item['qty'],

                    'estimasi_harga' =>
                        $item['estimasi_harga'] ?? 0,

                    'qty_diterima' =>
                        0,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PENTING
            |--------------------------------------------------------------------------
            |
            | Tidak ada:
            |
            | $varian->increment('stok', ...)
            |
            | karena ini baru PERENCANAAN.
            |
            */
        });

        return redirect()
            ->route('perencanaan-pembelian.index')
            ->with(
                'success',
                'Perencanaan pembelian berhasil dibuat.'
            );
    }

    /**
     * Menampilkan detail satu perencanaan.
     */
    public function show(
        PerencanaanPembelian $perencanaanPembelian
    ) {
        $perencanaanPembelian->load([
            'supplier',
            'user',
            'details.varian.barang',
        ]);

        return view(
            'perencanaan-pembelian.show',
            [
                'perencanaan' => $perencanaanPembelian
            ]
        );
    }

    /**
     * Membatalkan perencanaan.
     *
     * Kita tidak langsung delete supaya histori tetap ada.
     */
    public function cancel(
        PerencanaanPembelian $perencanaanPembelian
    ) {
        /*
        | Jangan izinkan pembatalan jika
        | sudah ada barang yang diterima.
        */

        $sudahDiterima =
            $perencanaanPembelian
                ->details()
                ->where(
                    'qty_diterima',
                    '>',
                    0
                )
                ->exists();

        if ($sudahDiterima) {

            return back()->with(
                'error',
                'Perencanaan tidak dapat dibatalkan karena sudah memiliki penerimaan barang.'
            );
        }

        $perencanaanPembelian->update([
            'status' => 'dibatalkan'
        ]);

        return redirect()
            ->route('perencanaan-pembelian.index')
            ->with(
                'success',
                'Perencanaan pembelian berhasil dibatalkan.'
            );
    }
}