<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\DetailPembelian;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::with('supplier')
            ->latest()
            ->get();

        return view('pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $supplier = Supplier::all();

        $varian = \App\Models\VarianBarang::with('barang')
            ->get();

        return view(
            'pembelian.create',
            compact(
                'supplier',
                'varian'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required',
            'tanggal_pembelian' => 'required',
            'supplier_id' => 'required',
            'cart' => 'required|array|min:1',
            'cart.*.varian_id' => 'required|integer|exists:varian_barang,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.harga_beli' => 'required|numeric|min:0',
            'cart.*.diskon' => 'nullable|numeric|min:0',
        ]);

        $pembelian = Pembelian::create([
            'no_faktur' => $request->no_faktur,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'total_brutto' => 0,
            'total_diskon' => 0,
            'total_netto' => 0,
        ]);

        $totalBrutto = 0;
        $totalDiskon = 0;

        foreach ($request->cart as $item) {

            $varian = \App\Models\VarianBarang::findOrFail(
                $item['varian_id']
            );

            $subtotal =
                ($item['qty'] * $item['harga_beli']);

            \App\Models\DetailPembelian::create([
                'pembelian_id' => $pembelian->id,
                'varian_id' => $item['varian_id'],
                'qty' => $item['qty'],
                'harga_satuan' => $item['harga_beli'],
                'diskon' => $item['diskon'],
                'subtotal' => $subtotal
            ]);

            $varian->increment(
                'stok',
                $item['qty']
            );

            $varian->update([
                'harga_beli' => $item['harga_beli']
            ]);

            $totalBrutto += $subtotal;
            $totalDiskon += $item['diskon'];
        }

        $pembelian->update([
            'total_brutto' => $totalBrutto,
            'total_diskon' => $totalDiskon,
            'total_netto' => $totalBrutto - $totalDiskon
        ]);

        return redirect()
            ->route('pembelian.index')
            ->with(
                'success',
                'Pembelian berhasil disimpan'
            );
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Pembelian $pembelian)
    {
        $supplier = Supplier::all();

        return view(
            'pembelian.edit',
            compact('pembelian', 'supplier')
        );
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'no_faktur' => 'required',
            'tanggal_pembelian' => 'required',
            'supplier_id' => 'required',
        ]);

        $pembelian->update([
            'no_faktur' => $request->no_faktur,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()
            ->route('pembelian.index')
            ->with('success', 'Pembelian berhasil diupdate');
    }

    public function destroy(Pembelian $pembelian)
    {
        $pembelian->delete();

        return redirect()
            ->route('pembelian.index')
            ->with('success', 'Pembelian berhasil dihapus');
    }

    public function struk($id)
    {
        $pembelian = Pembelian::with('supplier', 'user')->findOrFail($id);

        $detail = DetailPembelian::with('varian.barang')
            ->where('pembelian_id', $pembelian->id)
            ->get();

        return view('pembelian.struk', compact('pembelian', 'detail'));
    }
}
