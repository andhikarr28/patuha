@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold">Integrasi Marketplace</h1>
        <p class="text-gray-500 text-sm">Kelola integrasi produk, stok, dan pesanan antara sistem dengan Shopee.</p>
    </div>

    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    @if(session('sync_stock_results'))
        <div class="border rounded">
            <div class="px-4 py-3 border-b">
                <h2 class="font-bold">Hasil Rekonsiliasi Stok Shopee</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-gray-500 border-b"><th class="px-4 py-2">Varian</th><th class="px-4 py-2 text-right">Lokal Sebelum</th><th class="px-4 py-2 text-right">Shopee</th><th class="px-4 py-2 text-right">Lokal Sesudah</th><th class="px-4 py-2">Keterangan</th></tr></thead>
                    <tbody>
                        @foreach(session('sync_stock_results') as $hasil)
                            <tr class="border-b"><td class="px-4 py-2">{{ $hasil['varian'] }}</td><td class="px-4 py-2 text-right">{{ $hasil['stok_lokal_sebelum'] ?? '-' }}</td><td class="px-4 py-2 text-right">{{ $hasil['stok_shopee'] ?? '-' }}</td><td class="px-4 py-2 text-right">{{ $hasil['stok_lokal_sesudah'] ?? '-' }}</td><td class="px-4 py-2">{{ $hasil['keterangan'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(session('sync_order_results'))
        <div class="border rounded">
            <div class="px-4 py-3 border-b"><h2 class="font-bold">Hasil Import Order Shopee</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-gray-500 border-b"><th class="px-4 py-2">Order</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Keterangan</th></tr></thead>
                    <tbody>
                        @foreach(session('sync_order_results') as $hasil)
                            <tr class="border-b"><td class="px-4 py-2">{{ $hasil['order_sn'] ?? '-' }}</td><td class="px-4 py-2 font-semibold {{ ($hasil['status'] ?? '') === 'GAGAL' ? 'text-red-600' : 'text-green-700' }}">{{ $hasil['status'] ?? '-' }}</td><td class="px-4 py-2">{{ $hasil['keterangan'] ?? '-' }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- STATUS KONEKSI --}}
    <div class="border rounded p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Status Shopee</p>
            @if($marketplace?->status)
                <p class="font-bold text-green-600 mt-1">● Terhubung</p>
                <p class="text-sm text-gray-500 mt-1">
                    Terakhir sinkron:
                    {{ $marketplace->last_sync ? \Carbon\Carbon::parse($marketplace->last_sync)->format('d M Y H:i') : 'Belum pernah' }}
                </p>
            @else
                <p class="font-bold text-red-600 mt-1">● Tidak Terhubung</p>
                <p class="text-sm text-gray-500 mt-1">Hubungkan akun Shopee untuk menggunakan fitur sinkronisasi.</p>
            @endif
        </div>

        @if($marketplace?->status)
            <a href="{{ route('shopee.auth') }}" onclick="return confirm('Hubungkan ulang akun Shopee?')"
                class="border border-orange-300 bg-orange-50 text-orange-700 rounded px-4 py-2 text-sm font-semibold text-center">
                ↻ Hubungkan Ulang
            </a>
        @else
            <a href="{{ route('shopee.auth') }}" class="bg-orange-500 text-white rounded px-4 py-2 text-sm font-semibold text-center">
                Hubungkan Shopee →
            </a>
        @endif
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Produk Shopee</p>
            <p class="text-xl font-bold">{{ $jumlahProduk }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Varian Shopee</p>
            <p class="text-xl font-bold">{{ $jumlahVarian }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">SKU Termapping</p>
            <p class="text-xl font-bold">{{ $jumlahMapping }}</p>
            <p class="text-xs text-gray-400">dari {{ $jumlahVarian }} varian</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Sinkronisasi Terakhir</p>
            @if($marketplace?->last_sync)
                <p class="font-bold">{{ \Carbon\Carbon::parse($marketplace->last_sync)->format('d M Y H:i') }} WIB</p>
            @else
                <p class="font-bold">Belum Pernah</p>
            @endif
        </div>
    </div>

    {{-- SETUP INTEGRASI --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b">
            <h2 class="font-bold">Setup Integrasi Produk</h2>
            <p class="text-sm text-gray-500">Lakukan tahapan berikut secara berurutan.</p>
        </div>

        <div class="p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- STEP 1 --}}
            <div class="border rounded p-4">
                <p class="font-bold mb-1">1. Sinkron Produk</p>
                <p class="text-sm text-gray-500 mb-3">Ambil daftar produk dari toko Shopee.</p>
                <form action="{{ route('marketplace.sync.products') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">🔄 Sinkron Produk</button>
                </form>
            </div>

            {{-- STEP 2 --}}
            <div class="border rounded p-4">
                <p class="font-bold mb-1">2. Sinkron Variasi</p>
                <p class="text-sm text-gray-500 mb-3">Ambil warna, ukuran, SKU dari tiap produk.</p>
                <form action="{{ route('marketplace.sync.variants') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-purple-600 text-white rounded px-4 py-2 text-sm font-semibold">🎨 Sinkron Variasi</button>
                </form>
            </div>

            {{-- STEP 3 --}}
            <div class="border rounded p-4">
                <p class="font-bold mb-1">3. Mapping SKU</p>
                <p class="text-sm text-gray-500 mb-3">Hubungkan varian Shopee dengan varian lokal.</p>
                <a href="{{ route('marketplace.mappings') }}" class="block text-center bg-amber-500 text-white rounded px-4 py-2 text-sm font-semibold">🔗 Kelola Mapping SKU</a>
            </div>

        </div>
    </div>

    {{-- OPERASIONAL --}}
    <div>
        <h2 class="font-bold mb-3">Sinkronisasi Operasional</h2>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

            {{-- STOK --}}
            <div class="border rounded p-4">
                <h3 class="font-semibold mb-1">Sinkronisasi Stok</h3>
                <p class="text-sm text-gray-500 mb-3">Samakan jumlah stok antara marketplace dan sistem lokal.</p>

                <div class="grid grid-cols-2 gap-2">
                    <form action="{{ route('marketplace.sync.stocks') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full border border-green-300 bg-green-50 text-green-700 rounded px-4 py-2 text-sm font-semibold">↓ Shopee → Lokal</button>
                    </form>
                    <form action="{{ route('marketplace.sync.local-stocks') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white rounded px-4 py-2 text-sm font-semibold">↑ Lokal → Shopee</button>
                    </form>
                </div>

                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 mt-3">
                    ⚠️ Pastikan mapping SKU sudah benar sebelum sinkronisasi stok.
                </p>
            </div>

            {{-- ORDER --}}
            <div class="border rounded p-4">
                <h3 class="font-semibold mb-1">Sinkronisasi Pesanan</h3>
                <p class="text-sm text-gray-500 mb-3">Ambil transaksi terbaru dari Shopee ke sistem.</p>

                <form action="{{ route('marketplace.sync.orders') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-cyan-600 text-white rounded px-4 py-2 text-sm font-semibold">🛒 Ambil Pesanan dari Shopee</button>
                </form>

                <p class="text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded p-2 mt-3">
                    Pesanan yang diambil akan diproses sebagai penjualan marketplace dan menyesuaikan stok lokal.
                </p>
            </div>

        </div>
    </div>

    {{-- QUICK ACCESS --}}
    <div class="border rounded p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h3 class="font-semibold">Data Marketplace</h3>
            <p class="text-sm text-gray-500">Lihat data produk yang telah diambil dari Shopee.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('marketplace.products') }}" class="bg-slate-100 text-slate-700 rounded px-4 py-2 text-sm font-semibold">📦 Lihat Produk</a>
            <a href="{{ route('marketplace.mappings') }}" class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">🔗 Mapping SKU</a>
        </div>
    </div>

</div>
@endsection
