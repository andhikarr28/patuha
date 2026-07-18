<?php

namespace App\Http\Controllers;

use App\Models\VarianBarang;
use App\Models\Barang;
use Illuminate\Http\Request;

class VarianBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $varian = VarianBarang::with('barang')->latest()->get();

        return view('varian.index', compact('varian'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $barang = Barang::orderBy('nama_barang')
            ->get();

        $selectedBarang = null;

        if ($request->filled('barang_id')) {

            $selectedBarang = Barang::findOrFail(
                $request->barang_id
            );

        }

        return view(
            'varian.create',
            compact(
                'barang',
                'selectedBarang'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'warna' => 'required',
            'ukuran' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        $varian = VarianBarang::create([
            'barang_id' => $request->barang_id,
            'warna' => $request->warna,
            'ukuran' => $request->ukuran,
            'sku' => $request->sku,
            'harga_beli' => $request->harga_beli ?? 0,
            'harga_jual' => $request->harga_jual,
            'stok' => 0,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()
            ->route('barang.show', $varian->barang_id)
            ->with(
                'success',
                'Varian berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VarianBarang $varian)
    {
        $varian->load('barang');

        return view(
            'varian.edit',
            compact('varian')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VarianBarang $varian)
    {
        $request->validate([
            'barang_id' => 'required',
            'warna' => 'required',
            'ukuran' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        $varian->update([
            'barang_id' => $request->barang_id,
            'warna' => $request->warna,
            'ukuran' => $request->ukuran,
            'sku' => $request->sku,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()
            ->route('barang.show', $varian->barang_id)
            ->with(
                'success',
                'Varian berhasil diperbarui.'
            );
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VarianBarang $varian)
    {
        $varian->delete();

        return redirect()
            ->route('varian.index')
            ->with('success', 'Varian berhasil dihapus');
    }
}
