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
                <a href="{{ route('barang.edit', $barang->id) }}" class="border rounded px-4 py-2 text-sm font-semibold">✏️
                    Edit Barang</a>
                <a href="{{ route('varian.create', ['barang_id' => $barang->id]) }}"
                    class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Varian</a>
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
                <div
                    class="w-full md:w-40 h-40 bg-gray-100 flex items-center justify-center rounded overflow-hidden shrink-0">
                    @if($barang->foto)
                        <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}"
                            class="w-full h-full object-cover">
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
                            <p class="text-gray-500">Supplier</p>
                            <p class="font-semibold">{{ $barang->supplier?->nama_supplier ?? '-' }}</p>
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
            <div class="flex flex-col gap-3 px-4 py-3 border-b md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="font-bold">Varian Produk</h2>
                    <p class="text-sm text-gray-500">Kelola warna, ukuran, harga, dan stok.</p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- SEARCH BOX --}}
                    <div class="relative">
                        <input
                            type="text"
                            id="searchVarian"
                            placeholder="Cari warna, ukuran, atau SKU..."
                            class="border rounded pl-8 pr-3 py-1.5 text-sm w-56 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            onkeyup="filterVarian()"
                        >
                        <svg class="w-4 h-4 text-gray-400 absolute left-2 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <span class="bg-gray-100 text-sm rounded px-3 py-1 whitespace-nowrap" id="varianCount">{{ $jumlahVarian }} Varian</span>
                </div>
            </div>

            @if($barang->varians->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="tableVarian">
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
                                    $searchKey = strtolower(($varian->warna ?: '') . ' ' . ($varian->ukuran ?: '') . ' ' . ($varian->sku ?: '') . ' ' . $varian->id);
                                @endphp
                                <tr class="border-b" data-search="{{ $searchKey }}">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold">{{ $varian->warna ?: '-' }} / {{ $varian->ukuran ?: '-' }}</p>
                                        <p class="text-xs text-gray-400">ID: {{ $varian->id }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $varian->sku ?: '-' }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                                    <td
                                        class="px-4 py-3 {{ $margin > 0 ? 'text-green-600' : ($margin < 0 ? 'text-red-600' : '') }}">
                                        {{ $margin > 0 ? '+' : '' }}Rp {{ number_format($margin, 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-center {{ $stokMenipisVarian ? 'text-red-600 font-bold' : 'font-semibold' }}">
                                        {{ $varian->stok }}
                                        @if($stokMenipisVarian) <span class="text-xs">(Menipis)</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $varian->stok_minimum }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('varian.edit', $varian->id) }}"
                                                class="bg-amber-500 text-white rounded px-3 py-1 text-xs">Edit</a>
                                            <form action="{{ route('varian.destroy', $varian->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus varian ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-600 text-white rounded px-3 py-1 text-xs">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pesan jika hasil pencarian kosong --}}
                    <div id="emptySearch" class="text-center py-8 px-4 hidden">
                        <p class="font-semibold">Tidak ditemukan</p>
                        <p class="text-sm text-gray-500 mt-1">Tidak ada varian yang cocok dengan pencarian.</p>
                    </div>
                </div>
            @else
                <div class="text-center py-10 px-4">
                    <p class="font-semibold">Belum Ada Varian</p>
                    <p class="text-sm text-gray-500 mt-1">Barang ini belum memiliki varian. Tambahkan warna, ukuran, SKU, harga,
                        dan stok.</p>
                    <a href="{{ route('varian.create', ['barang_id' => $barang->id]) }}"
                        class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah
                        Varian</a>
                </div>
            @endif
        </div>

        {{-- RIWAYAT HARGA BELI --}}
        @php
            $variansDenganRiwayat = $barang->varians
                ->filter(fn ($varian) => $varian->detailPembelian->isNotEmpty());
        @endphp
        <div class="border rounded">
            <div class="px-4 py-3 border-b">
                <h2 class="font-bold">Riwayat Harga Beli</h2>
                <p class="text-sm text-gray-500">Berdasarkan penerimaan pembelian yang sudah diterima.</p>
            </div>

            @forelse($variansDenganRiwayat as $varian)
                <div class="p-4 border-b last:border-b-0">
                    <div class="mb-3">
                        <p class="font-semibold">{{ $varian->warna ?: '-' }} / {{ $varian->ukuran ?: '-' }}</p>
                        <p class="text-xs text-gray-500">SKU: {{ $varian->sku ?: '-' }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pr-4">Tanggal</th>
                                    <th class="py-2 pr-4">No. Faktur</th>
                                    <th class="py-2 pr-4">Supplier</th>
                                    <th class="py-2 pr-4 text-center">Qty</th>
                                    <th class="py-2 pr-4 text-right">Harga / Unit</th>
                                    <th class="py-2 text-right">Diskon</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($varian->detailPembelian->sortByDesc(fn ($detail) => $detail->pembelian->tanggal_pembelian) as $detail)
                                    <tr class="border-b last:border-b-0">
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($detail->pembelian->tanggal_pembelian)->format('d M Y') }}
                                        </td>
                                        <td class="py-2 pr-4 whitespace-nowrap">{{ $detail->pembelian->no_faktur }}</td>
                                        <td class="py-2 pr-4">{{ $detail->pembelian->supplier?->nama_supplier ?? '-' }}</td>
                                        <td class="py-2 pr-4 text-center">{{ $detail->qty }}</td>
                                        <td class="py-2 pr-4 text-right whitespace-nowrap">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="py-2 text-right text-red-600 whitespace-nowrap">Rp {{ number_format($detail->diskon, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 px-4 text-sm text-gray-500">
                    Belum ada riwayat penerimaan pembelian untuk varian barang ini.
                </div>
            @endforelse
        </div>

    </div>

    <script>
        function filterVarian() {
            const keyword = document.getElementById('searchVarian').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#tableVarian tbody tr');
            const emptyMsg = document.getElementById('emptySearch');
            let visibleCount = 0;

            rows.forEach(row => {
                const match = row.dataset.search.includes(keyword);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('varianCount').textContent = visibleCount + ' Varian';

            if (emptyMsg) {
                emptyMsg.classList.toggle('hidden', visibleCount !== 0);
            }
        }
    </script>
@endsection
