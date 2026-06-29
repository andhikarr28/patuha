<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\VarianBarang;
use App\Models\DetailPenjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::latest()->get();

        return view(
            'penjualan.index',
            compact('penjualan')
        );
    }

    public function create()
    {
        $varian = VarianBarang::with('barang')
            ->get();

        return view(
            'penjualan.create',
            compact('varian')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_nota' => 'required|unique:penjualan,no_nota',
            'tanggal_penjualan' => 'required',
            'channel' => 'required',
            'metode_pembayaran' => 'required',
            'cart' => 'required|array|min:1',
        ]);

        $penjualan = Penjualan::create([
            'no_nota' => $request->no_nota,
            'tanggal_penjualan' => $request->tanggal_penjualan,
            'channel' => $request->channel,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total' => 0,
            'user_id' => auth()->id(),
        ]);

        $grandTotal = 0;

        foreach ($request->cart as $item) {

            $varian = VarianBarang::findOrFail(
                $item['varian_id']
            );

            if ($varian->stok < $item['qty']) {

                return back()->with(
                    'error',
                    'Stok ' .
                    $varian->barang->nama_barang .
                    ' tidak mencukupi'
                );
            }

            $subtotal =
                $varian->harga_jual *
                $item['qty'];

            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'varian_id' => $varian->id,
                'qty' => $item['qty'],
                'harga' => $varian->harga_jual,
                'subtotal' => $subtotal,
            ]);

            $varian->decrement(
                'stok',
                $item['qty']
            );

            $grandTotal += $subtotal;
        }

        $penjualan->update([
            'total' => $grandTotal
        ]);

        return redirect()
            ->route('penjualan.index')
            ->with(
                'success',
                'Transaksi berhasil disimpan'
            );
    }

    public function show(Penjualan $penjualan)
    {
        //
    }

    public function edit(Penjualan $penjualan)
    {
        return view(
            'penjualan.edit',
            compact('penjualan')
        );
    }

    public function update(
        Request $request,
        Penjualan $penjualan
    ) {
        $request->validate([
            'no_nota' => 'required',
            'tanggal_penjualan' => 'required',
            'channel' => 'required',
            'metode_pembayaran' => 'required',
        ]);

        $penjualan->update([
            'no_nota' => $request->no_nota,
            'tanggal_penjualan' => $request->tanggal_penjualan,
            'channel' => $request->channel,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route(
            'detail-penjualan.create',
            [
                'penjualan_id' => $penjualan->id
            ]
        );
    }

    public function destroy(
        Penjualan $penjualan
    ) {
        $penjualan->delete();

        return redirect()
            ->route('penjualan.index')
            ->with(
                'success',
                'Penjualan berhasil dihapus'
            );
    }
}