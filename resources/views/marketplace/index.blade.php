@extends('layouts.app')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <h1 class="text-3xl font-bold text-slate-900">
                    Integrasi Marketplace
                </h1>

                <p class="text-slate-500 mt-1">
                    Kelola integrasi produk, stok, dan pesanan antara sistem dengan Shopee.
                </p>

            </div>


            {{-- STATUS KONEKSI MARKETPLACE --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Status Shopee
                        </p>

                        @if($marketplace?->status)

                                        <div class="flex items-center gap-2 mt-2">

                                            <span class="relative flex h-3 w-3">

                                                <span class="absolute inline-flex h-full w-full
                                                         rounded-full bg-green-400 opacity-75
                                                         animate-ping">
                                                </span>

                                                <span class="relative inline-flex
                                                         rounded-full h-3 w-3
                                                         bg-green-500">
                                                </span>

                                            </span>

                                            <span class="font-bold text-green-600">
                                                Terhubung
                                            </span>

                                        </div>

                                        <p class="text-sm text-slate-500 mt-3">

                                            Terakhir sinkron:

                                            <span class="font-medium text-slate-700">

                                                {{ $marketplace->last_sync
                            ? \Carbon\Carbon::parse($marketplace->last_sync)
                                ->format('d M Y H:i')
                            : 'Belum pernah'
                                            }}

                                            </span>

                                        </p>

                        @else

                            <div class="flex items-center gap-2 mt-2">

                                <span class="inline-flex
                                         rounded-full h-3 w-3
                                         bg-red-500">
                                </span>

                                <span class="font-bold text-red-600">
                                    Tidak Terhubung
                                </span>

                            </div>

                            <p class="text-sm text-slate-500 mt-3">
                                Hubungkan akun Shopee untuk menggunakan
                                fitur sinkronisasi marketplace.
                            </p>

                        @endif

                    </div>


                    {{-- ACTION KONEKSI --}}
                    <div>

                        @if($marketplace?->status)

                                <a href="{{ route('shopee.auth') }}" onclick="return confirm(
                                   'Hubungkan ulang akun Shopee? Anda akan diarahkan ke halaman otorisasi Shopee.'
                               )" class="inline-flex items-center gap-2
                                      border border-orange-200
                                      bg-orange-50
                                      hover:bg-orange-100
                                      text-orange-700
                                      px-4 py-2.5
                                      rounded-xl
                                      text-sm font-semibold
                                      transition">

                                    ↻ Hubungkan Ulang

                                </a>

                        @else

                            <a href="{{ route('shopee.auth') }}" class="inline-flex items-center gap-2
                                  bg-orange-500
                                  hover:bg-orange-600
                                  text-white
                                  px-4 py-2.5
                                  rounded-xl
                                  text-sm font-semibold
                                  shadow-sm
                                  transition">

                                Hubungkan Shopee

                                <span>→</span>

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- PRODUK --}}
            <div class="bg-white border border-slate-200
                        rounded-2xl p-6">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Produk Shopee
                        </p>

                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $jumlahProduk }}
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            produk tersinkron
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl
                                bg-orange-50
                                flex items-center justify-center text-xl">
                        📦
                    </div>

                </div>

            </div>


            {{-- VARIAN --}}
            <div class="bg-white border border-slate-200
                        rounded-2xl p-6">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Varian Shopee
                        </p>

                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $jumlahVarian }}
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            variasi produk
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl
                                bg-purple-50
                                flex items-center justify-center text-xl">
                        🎨
                    </div>

                </div>

            </div>


            {{-- MAPPING --}}
            <div class="bg-white border border-slate-200
                        rounded-2xl p-6">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            SKU Termapping
                        </p>

                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $jumlahMapping }}
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            dari {{ $jumlahVarian }} varian
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl
                                bg-blue-50
                                flex items-center justify-center text-xl">
                        🔗
                    </div>

                </div>

            </div>


            {{-- LAST SYNC --}}
            <div class="bg-white border border-slate-200
                        rounded-2xl p-6">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Sinkronisasi Terakhir
                        </p>

                        @if($marketplace?->last_sync)

                                            <p class="text-lg font-bold text-slate-900 mt-2">

                                                {{ \Carbon\Carbon::parse(
                                $marketplace->last_sync
                            )->format('d M Y') }}

                                            </p>

                                            <p class="text-sm text-slate-500">

                                                {{ \Carbon\Carbon::parse(
                                $marketplace->last_sync
                            )->format('H:i') }} WIB

                                            </p>

                        @else

                            <p class="text-lg font-bold text-slate-900 mt-2">
                                Belum Pernah
                            </p>

                        @endif

                    </div>

                    <div class="w-12 h-12 rounded-xl
                                bg-green-50
                                flex items-center justify-center text-xl">
                        🔄
                    </div>

                </div>

            </div>

        </div>


        {{-- ALUR SETUP --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-xl font-bold text-slate-900">
                    Setup Integrasi Produk
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Lakukan tahapan berikut secara berurutan untuk menghubungkan produk Shopee dengan stok lokal.
                </p>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    {{-- STEP 1 --}}
                    <div class="relative border border-slate-200
                                rounded-2xl p-5">

                        <div class="flex items-center gap-4 mb-4">

                            <div class="w-11 h-11 rounded-xl
                                        bg-blue-600 text-white
                                        flex items-center justify-center
                                        font-bold">
                                1
                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Sinkron Produk
                                </h3>

                                <p class="text-xs text-slate-500">
                                    Ambil data produk
                                </p>

                            </div>

                        </div>

                        <p class="text-sm text-slate-600 mb-5">
                            Mengambil daftar produk dari toko Shopee dan menyimpannya ke sistem lokal.
                        </p>

                        <form action="{{ route('marketplace.sync.products') }}" method="POST">

                            @csrf

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700
                                       text-white font-semibold
                                       px-4 py-3 rounded-xl transition">

                                🔄 Sinkron Produk

                            </button>

                        </form>

                    </div>


                    {{-- STEP 2 --}}
                    <div class="relative border border-slate-200
                                rounded-2xl p-5">

                        <div class="flex items-center gap-4 mb-4">

                            <div class="w-11 h-11 rounded-xl
                                        bg-purple-600 text-white
                                        flex items-center justify-center
                                        font-bold">
                                2
                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Sinkron Variasi
                                </h3>

                                <p class="text-xs text-slate-500">
                                    Ambil varian produk
                                </p>

                            </div>

                        </div>

                        <p class="text-sm text-slate-600 mb-5">
                            Mengambil variasi seperti warna, ukuran, SKU, dan model dari setiap produk Shopee.
                        </p>

                        <form action="{{ route('marketplace.sync.variants') }}" method="POST">

                            @csrf

                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700
                                       text-white font-semibold
                                       px-4 py-3 rounded-xl transition">

                                🎨 Sinkron Variasi

                            </button>

                        </form>

                    </div>


                    {{-- STEP 3 --}}
                    <div class="relative border border-slate-200
                                rounded-2xl p-5">

                        <div class="flex items-center gap-4 mb-4">

                            <div class="w-11 h-11 rounded-xl
                                        bg-amber-500 text-white
                                        flex items-center justify-center
                                        font-bold">
                                3
                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Mapping SKU
                                </h3>

                                <p class="text-xs text-slate-500">
                                    Hubungkan dengan lokal
                                </p>

                            </div>

                        </div>

                        <p class="text-sm text-slate-600 mb-5">
                            Hubungkan setiap varian Shopee dengan varian barang pada sistem berdasarkan SKU.
                        </p>

                        <a href="{{ route('marketplace.mappings') }}" class="block w-full text-center
                                   bg-amber-500 hover:bg-amber-600
                                   text-white font-semibold
                                   px-4 py-3 rounded-xl transition">

                            🔗 Kelola Mapping SKU

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- OPERASIONAL --}}
        <div>

            <div class="mb-4">

                <h2 class="text-xl font-bold text-slate-900">
                    Sinkronisasi Operasional
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Kelola stok dan transaksi setelah produk berhasil dimapping.
                </p>

            </div>


            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

                {{-- STOK --}}
                <div class="bg-white border border-slate-200
                            rounded-2xl p-6">

                    <div class="flex items-start gap-4 mb-6">

                        <div class="w-12 h-12 rounded-xl
                                    bg-green-50
                                    flex items-center justify-center text-xl">
                            📦
                        </div>

                        <div>

                            <h3 class="text-lg font-bold text-slate-900">
                                Sinkronisasi Stok
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Samakan jumlah stok antara marketplace dan sistem lokal.
                            </p>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <form action="{{ route('marketplace.sync.stocks') }}" method="POST">

                            @csrf

                            <button type="submit" class="w-full border border-green-200
                                       bg-green-50 hover:bg-green-100
                                       text-green-700 font-semibold
                                       px-4 py-3 rounded-xl transition">

                                ↓ Shopee → Lokal

                            </button>

                        </form>


                        <form action="{{ route('marketplace.sync.local-stocks') }}" method="POST">

                            @csrf

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700
                                       text-white font-semibold
                                       px-4 py-3 rounded-xl transition">

                                ↑ Lokal → Shopee

                            </button>

                        </form>

                    </div>


                    <div class="mt-4 p-4 bg-amber-50
                                border border-amber-100
                                rounded-xl">

                        <p class="text-xs text-amber-700 leading-relaxed">
                            ⚠️ Pastikan mapping SKU sudah benar sebelum melakukan sinkronisasi stok.
                        </p>

                    </div>

                </div>


                {{-- ORDER --}}
                <div class="bg-white border border-slate-200
                            rounded-2xl p-6">

                    <div class="flex items-start gap-4 mb-6">

                        <div class="w-12 h-12 rounded-xl
                                    bg-cyan-50
                                    flex items-center justify-center text-xl">
                            🛒
                        </div>

                        <div>

                            <h3 class="text-lg font-bold text-slate-900">
                                Sinkronisasi Pesanan
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Ambil transaksi terbaru dari Shopee ke sistem.
                            </p>

                        </div>

                    </div>


                    <form action="{{ route('marketplace.sync.orders') }}" method="POST">

                        @csrf

                        <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700
                                   text-white font-semibold
                                   px-4 py-3 rounded-xl transition">

                            🛒 Ambil Pesanan dari Shopee

                        </button>

                    </form>


                    <div class="mt-4 p-4 bg-blue-50
                                border border-blue-100
                                rounded-xl">

                        <p class="text-xs text-blue-700 leading-relaxed">
                            Pesanan yang berhasil diambil akan diproses sebagai penjualan marketplace dan menyesuaikan stok
                            lokal.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- QUICK ACCESS --}}
        <div class="bg-white border border-slate-200
                    rounded-2xl p-6">

            <div class="flex flex-col lg:flex-row
                        lg:items-center lg:justify-between gap-5">

                <div>

                    <h3 class="font-bold text-slate-900">
                        Data Marketplace
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Lihat data produk yang telah berhasil diambil dari Shopee.
                    </p>

                </div>

                <div class="flex flex-wrap gap-3">

                    <a href="{{ route('marketplace.products') }}" class="inline-flex items-center gap-2
                               bg-slate-100 hover:bg-slate-200
                               text-slate-700 font-semibold
                               px-4 py-3 rounded-xl transition">

                        📦 Lihat Produk Shopee

                    </a>

                    <a href="{{ route('marketplace.mappings') }}" class="inline-flex items-center gap-2
                               bg-slate-900 hover:bg-slate-800
                               text-white font-semibold
                               px-4 py-3 rounded-xl transition">

                        🔗 Mapping SKU

                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection