<?php

namespace App\Http\Controllers;

use App\Models\VarianBarang;
use App\Models\Barang;
use App\Services\SkuGeneratorService;
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
    public function store(Request $request, SkuGeneratorService $skuGenerator)
    {
        $validated = $request->validate([
            'barang_id'    => 'required|exists:barang,id',
            'warna'        => 'required|string|max:50',
            'ukuran'       => 'required|string|max:20',
            'harga_beli'   => 'nullable|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        // Cegah kombinasi warna + ukuran yang sama untuk barang yang sama
        $sudahAda = VarianBarang::where('barang_id', $barang->id)
            ->where('warna', $validated['warna'])
            ->where('ukuran', $validated['ukuran'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->withErrors([
                    'warna' => 'Varian dengan warna dan ukuran ini sudah ada untuk barang tersebut.',
                ]);
        }

        $sku = $skuGenerator->generate(
            $barang,
            $validated['warna'],
            $validated['ukuran']
        );

        $varian = VarianBarang::create([
            'barang_id'    => $barang->id,
            'warna'        => $validated['warna'],
            'ukuran'       => $validated['ukuran'],
            'sku'          => $sku,
            'harga_beli'   => $validated['harga_beli'] ?? 0,
            'harga_jual'   => $validated['harga_jual'],
            'stok'         => 0,
            'stok_minimum' => $validated['stok_minimum'] ?? 5,
        ]);

        return redirect()
            ->route('barang.show', $varian->barang_id)
            ->with(
                'success',
                "Varian berhasil ditambahkan dengan SKU {$varian->sku}."
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
    public function update(Request $request, VarianBarang $varian, SkuGeneratorService $skuGenerator)
    {
        $validated = $request->validate([
            'barang_id'    => 'required|exists:barang,id',
            'warna'        => 'required|string|max:50',
            'ukuran'       => 'required|string|max:20',
            'harga_beli'   => 'nullable|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        // Cegah kombinasi warna + ukuran yang sama untuk barang yang sama
        // (kecuali itu memang varian yang sedang diedit ini sendiri)
        $sudahAda = VarianBarang::where('barang_id', $barang->id)
            ->where('warna', $validated['warna'])
            ->where('ukuran', $validated['ukuran'])
            ->where('id', '!=', $varian->id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->withErrors([
                    'warna' => 'Varian dengan warna dan ukuran ini sudah ada untuk barang tersebut.',
                ]);
        }

        // SKU dihitung ulang kalau barang, warna, atau ukuran berubah
        $sku = $skuGenerator->generate(
            $barang,
            $validated['warna'],
            $validated['ukuran']
        );

        $varian->update([
            'barang_id'    => $barang->id,
            'warna'        => $validated['warna'],
            'ukuran'       => $validated['ukuran'],
            'sku'          => $sku,
            'harga_beli'   => $validated['harga_beli'] ?? 0,
            'harga_jual'   => $validated['harga_jual'],
            'stok'         => $validated['stok'],
            'stok_minimum' => $validated['stok_minimum'] ?? 5,
        ]);

        return redirect()
            ->route('barang.show', $varian->barang_id)
            ->with(
                'success',
                "Varian berhasil diperbarui dengan SKU {$varian->sku}."
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