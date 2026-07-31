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

    public function create()
    {
        $supplier = Supplier::orderBy('nama_supplier')
            ->get();

        $varian = VarianBarang::with([
            'barang.kategori'
        ])

            ->orderByRaw(
                'CASE
                    WHEN stok <= stok_minimum THEN 0
                    ELSE 1
                END'
            )
            ->orderBy('stok', 'asc')
            ->get();

        return view(
            'perencanaan-pembelian.create',
            compact(
                'supplier',
                'varian'
            )
        );
    }

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

        });

        return redirect()
            ->route('perencanaan-pembelian.index')
            ->with(
                'success',
                'Perencanaan pembelian berhasil dibuat.'
            );
    }

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

    public function cancel(
        PerencanaanPembelian $perencanaanPembelian
    ) {

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

    public function struk($id)
    {
        $perencanaan = PerencanaanPembelian::with('supplier', 'user', 'details.varian.barang')
            ->findOrFail($id);

        return view('perencanaan-pembelian.struk', compact('perencanaan'));
    }
}