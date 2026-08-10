<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Services\KodeBarangGeneratorService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('kategori', 'supplier')
            ->withCount('varians')
            ->withSum('varians', 'stok');

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nama_barang',
                    'like',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'artikel',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'brand',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'kode_barang',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }
        if ($request->filled('kategori_id')) {

            $query->where(
                'kategori_id',
                $request->kategori_id
            );
        }

        $barang = $query
            ->latest()
            ->get();

        $kategori = Kategori::orderBy(
            'nama_kategori'
        )->get();

        return view(
            'barang.index',
            compact(
                'barang',
                'kategori'
            )
        );
    }

    public function create()
    {
        $kategori = Kategori::orderBy(
            'nama_kategori'
        )->get();

        $supplier = Supplier::orderBy(
            'nama_supplier'
        )->get();

        return view(
            'barang.create',
            compact('kategori', 'supplier')
        );
    }

    public function store(Request $request, KodeBarangGeneratorService $kodeGenerator)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'supplier_id' => 'required|exists:supplier,id',
            'nama_barang' => 'required|string|max:255',
            'artikel' => 'required|string|max:255',
            'kode_seri' => 'required|string|max:50',
            'brand' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'kategori_id',
            'supplier_id',
            'nama_barang',
            'artikel',
            'kode_seri',
            'brand',
            'spesifikasi',
        ]);

        $kategori = Kategori::findOrFail($data['kategori_id']);

        $kodeBarang = $kodeGenerator->generate(
            $kategori->kode ?? '',
            $data['brand'] ?? null,
            $data['artikel'] ?? null,
            $data['kode_seri']
        );

        if ($kodeGenerator->sudahDipakai($kodeBarang)) {

            return back()
                ->withInput()
                ->withErrors([
                    'kode_seri' => "Kode Barang \"{$kodeBarang}\" sudah dipakai. Ganti Kode Seri-nya.",
                ]);
        }

        $data['kode_barang'] = $kodeBarang;

        if ($request->hasFile('foto')) {

            $data['foto'] = $request
                ->file('foto')
                ->store(
                    'barang',
                    'public'
                );
        }

        $barang = Barang::create($data);

        return redirect()
            ->route(
                'barang.show',
                $barang->id
            )
            ->with(
                'success',
                "Barang berhasil ditambahkan dengan kode {$barang->kode_barang}. Silakan tambahkan varian barang."
            );
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'kategori',

            'varians' => function ($query) {

                $query->orderBy('warna')
                    ->orderBy('ukuran');
            }
        ]);

        $jumlahVarian =
            $barang->varians->count();

        $totalStok =
            $barang->varians->sum('stok');

        $stokMenipis =
            $barang->varians
                ->filter(function ($varian) {

                    return $varian->stok
                        <= $varian->stok_minimum;

                })
                ->count();

        return view(
            'barang.show',
            compact(
                'barang',
                'jumlahVarian',
                'totalStok',
                'stokMenipis'
            )
        );
    }


    public function edit(Barang $barang)
    {
        $kategori = Kategori::orderBy(
            'nama_kategori'
        )->get();

        $supplier = Supplier::orderBy(
            'nama_supplier'
        )->get();

        return view(
            'barang.edit',
            compact(
                'barang',
                'kategori',
                'supplier'
            )
        );
    }

    public function update(
        Request $request,
        Barang $barang,
        KodeBarangGeneratorService $kodeGenerator
    ) {

        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'supplier_id' => 'required|exists:supplier,id',
            'nama_barang' => 'required|string|max:255',
            'artikel' => 'required|string|max:255',
            'kode_seri' => 'required|string|max:50',
            'brand' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'kategori_id',
            'supplier_id',
            'nama_barang',
            'artikel',
            'kode_seri',
            'brand',
            'spesifikasi',
        ]);

        $kategori = Kategori::findOrFail($data['kategori_id']);

        $kodeBarang = $kodeGenerator->generate(
            $kategori->kode ?? '',
            $data['brand'] ?? null,
            $data['artikel'] ?? null,
            $data['kode_seri']
        );

        if ($kodeGenerator->sudahDipakai($kodeBarang, $barang->id)) {

            return back()
                ->withInput()
                ->withErrors([
                    'kode_seri' => "Kode Barang \"{$kodeBarang}\" sudah dipakai barang lain. Ganti Kode Seri-nya.",
                ]);
        }

        $data['kode_barang'] = $kodeBarang;

        if ($request->hasFile('foto')) {

            $data['foto'] = $request
                ->file('foto')
                ->store(
                    'barang',
                    'public'
                );
        }


        $barang->update($data);

        return redirect()
            ->route(
                'barang.show',
                $barang->id
            )
            ->with(
                'success',
                "Data barang berhasil diperbarui dengan kode {$barang->kode_barang}."
            );
    }

    public function destroy(Barang $barang)
    {

        if ($barang->varians()->exists()) {

            return back()->with(
                'error',
                'Barang tidak dapat dihapus karena masih memiliki varian.'
            );
        }

        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }
}