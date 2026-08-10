@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Master Barang</h1>
            <p class="text-gray-500 text-sm">Kelola data barang dan seluruh varian produk.</p>
        </div>
        <a href="{{ route('barang.create') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Barang</a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- SEARCH & FILTER --}}
    <form action="{{ route('barang.index') }}" method="GET" class="border rounded p-4 flex flex-col md:flex-row gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari nama barang, artikel, atau brand..."
            class="flex-1 border rounded px-3 py-2 text-sm">

        <select name="kategori_id" class="border rounded px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach($kategori as $item)
                <option value="{{ $item->id }}" {{ request('kategori_id') == $item->id ? 'selected' : '' }}>
                    {{ $item->nama_kategori }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">Cari</button>

        @if(request('search') || request('kategori_id'))
            <a href="{{ route('barang.index') }}" class="border rounded px-4 py-2 text-sm font-semibold text-center">Reset</a>
        @endif
    </form>

    {{-- DAFTAR BARANG --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b">
            <h2 class="font-bold">Daftar Barang</h2>
            <p class="text-sm text-gray-500">{{ $barang->count() }} barang ditemukan</p>
        </div>

        @if($barang->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">Barang</th>
                            <th class="px-4 py-2">Kategori</th>
                            <th class="px-4 py-2">Supplier</th>
                            <th class="px-4 py-2">Brand</th>
                            <th class="px-4 py-2 text-center">Varian</th>
                            <th class="px-4 py-2 text-center">Total Stok</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $item)
                            @php
                                $jumlahVarian = $item->varians_count ?? 0;
                                $totalStok = $item->varians_sum_stok ?? 0;
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden shrink-0 flex items-center justify-center">
                                            @if($item->foto)
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs text-gray-400">No Image</span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('barang.show', $item->id) }}" class="font-semibold text-blue-600">{{ $item->nama_barang }}</a>
                                            @if($item->artikel)
                                                <p class="text-xs text-gray-500">Artikel: {{ $item->artikel }}</p>
                                            @endif
                                            @if($item->kode_seri)
                                                <p class="text-xs text-gray-400">{{ $item->kode_seri }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->supplier?->nama_supplier ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->brand ?: '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $jumlahVarian }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($jumlahVarian == 0)
                                        <span class="text-gray-400">Belum ada varian</span>
                                    @elseif($totalStok <= 0)
                                        <span class="text-red-600 font-semibold">Habis</span>
                                    @else
                                        <span class="text-green-700 font-semibold">{{ number_format($totalStok, 0, ',', '.') }} unit</span>
                                    @endif
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('barang.show', $item->id) }}" class="bg-blue-600 text-white rounded px-3 py-1 text-xs">Detail</a>
                                        @if(auth()->user()->hasRole(['admin']))
                                        <a href="{{ route('barang.edit', $item->id) }}" class="border rounded px-3 py-1 text-xs">Edit</a>
                                        <form action="{{ route('barang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white rounded px-3 py-1 text-xs">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Barang Tidak Ditemukan</p>
                <p class="text-sm text-gray-500 mt-1">
                    @if(request('search') || request('kategori_id'))
                        Tidak ada barang yang sesuai dengan pencarian atau filter yang dipilih.
                    @else
                        Belum ada data barang. Tambahkan barang pertama untuk mulai mengelola produk.
                    @endif
                </p>

                @if(request('search') || request('kategori_id'))
                    <a href="{{ route('barang.index') }}" class="inline-block mt-3 bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">Reset Pencarian</a>
                @else
                    <a href="{{ route('barang.create') }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Barang</a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection