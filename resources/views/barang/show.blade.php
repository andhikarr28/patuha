@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <a href="{{ route('barang.index') }}" class="text-sm text-blue-600">← Kembali ke Master Barang</a>
            <h1 class="text-2xl font-bold">{{ $barang->nama_barang }}</h1>
            <p class="text-gray-500 text-sm">Informasi barang dan seluruh varian produk.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('barang.edit', $barang->id) }}" class="border rounded px-4 py-2 text-sm font-semibold">✏️ Edit Barang</a>
            <a href="{{ route('varian.create', ['barang_id' => $barang->id]) }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Varian</a>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- INFORMASI BARANG --}}
    <div class="border rounded p-4">
        <div class="flex flex-col md:flex-row gap-4">

            {{-- FOTO --}}
            <div class="w-full md:w-40 h-40 bg-gray-100 flex items-center justify-center rounded overflow-hidden shrink-0">
                @if($barang->foto)
                    <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover">
                @else
                    <span class="text-gray-400 text-sm">No Image</span>
                @endif
            </div>

            {{-- INFO --}}
            <div class="flex-1 text-sm">
                <span class="inline-block bg-blue-50 text-blue-700 rounded px-2 py-1 mb-3">
                    {{ $barang->kategori?->nama_kategori ?? 'Tanpa Kategori' }}
                </span>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-gray-500">Nama Barang</p>
                        <p class="font-semibold">{{ $barang->nama_barang }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Brand</p>
                        <p class="font-semibold">{{ $barang->brand ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Artikel</p>
                        <p class="font-semibold">{{ $barang->artikel ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Kode Seri</p>
                        <p class="font-semibold">{{ $barang->kode_seri ?: '-' }}</p>
                    </div>
                </div>

                @if($barang->spesifikasi)
                    <div class="mt-4 border-t pt-3">
                        <p class="text-gray-500">Spesifikasi</p>
                        <p class="mt-1">{{ $barang->spesifikasi }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Varian</p>
            <p class="text-xl font-bold">{{ $jumlahVarian }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Stok</p>
            <p class="text-xl font-bold">{{ number_format($totalStok, 0, ',', '.') }} unit</p>
        </div>
        <div class="border rounded p-3 {{ $stokMenipis > 0 ? 'bg-red-50 border-red-300' : '' }}">
            <p class="text-sm text-gray-500">Stok Menipis</p>
            <p class="text-xl font-bold {{ $stokMenipis > 0 ? 'text-red-600' : '' }}">{{ $stokMenipis }} varian</p>
        </div>
    </div>

    {{-- DAFTAR VARIAN --}}
    <div class="border rounded">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <div>
                <h2 class="font-bold">Varian Produk</h2>
                <p class="text-sm text-gray-500">Kelola warna, ukuran, harga, dan stok.</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $jumlahVarian }} Varian</span>
        </div>

        @if($barang->varians->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">Varian</th>
                            <th class="px-4 py-2">SKU</th>
                            <th class="px-4 py-2">Harga Beli</th>
                            <th class="px-4 py-2">Harga Jual</th>
                            <th class="px-4 py-2">Margin</th>
                            <th class="px-4 py-2 text-center">Stok</th>
                            <th class="px-4 py-2 text-center">Min.</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang->varians as $varian)
                            @php
                                $hargaBeli = $varian->harga_beli ?? 0;
                                $hargaJual = $varian->harga_jual ?? 0;
                                $margin = $hargaJual - $hargaBeli;
                                $stokMenipisVarian = $varian->stok <= $varian->stok_minimum;
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $varian->warna ?: '-' }} / {{ $varian->ukuran ?: '-' }}</p>
                                    <p class="text-xs text-gray-400">ID: {{ $varian->id }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $varian->sku ?: '-' }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 {{ $margin > 0 ? 'text-green-600' : ($margin < 0 ? 'text-red-600' : '') }}">
                                    {{ $margin > 0 ? '+' : '' }}Rp {{ number_format($margin, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center {{ $stokMenipisVarian ? 'text-red-600 font-bold' : 'font-semibold' }}">
                                    {{ $varian->stok }}
                                    @if($stokMenipisVarian) <span class="text-xs">(Menipis)</span> @endif
                                </td>
                                <td class="px-4 py-3 text-center">{{ $varian->stok_minimum }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('varian.edit', $varian->id) }}" class="bg-amber-500 text-white rounded px-3 py-1 text-xs">Edit</a>
                                        <form action="{{ route('varian.destroy', $varian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus varian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white rounded px-3 py-1 text-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Belum Ada Varian</p>
                <p class="text-sm text-gray-500 mt-1">Barang ini belum memiliki varian. Tambahkan warna, ukuran, SKU, harga, dan stok.</p>
                <a href="{{ route('varian.create', ['barang_id' => $barang->id]) }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Varian</a>
            </div>
        @endif
    </div>

</div>
@endsection