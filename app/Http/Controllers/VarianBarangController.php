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
    public function create()
    {
        $barang = Barang::all();

        return view('varian.create', compact('barang'));
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
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        VarianBarang::create([
            'barang_id' => $request->barang_id,
            'warna' => $request->warna,
            'ukuran' => $request->ukuran,
            'sku' => $request->sku,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()
            ->route('varian.index')
            ->with('success', 'Varian berhasil ditambahkan');
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
        $barang = Barang::all();

        return view('varian.edit', compact('varian', 'barang'));
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
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        $varian->update([
            'barang_id' => $request->barang_id,
            'warna' => $request->warna,
            'ukuran' => $request->ukuran,
            'sku' => $request->sku,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()
            ->route('varian.index')
            ->with('success', 'Varian berhasil diupdate');
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
