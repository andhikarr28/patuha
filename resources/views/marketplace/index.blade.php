@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        Marketplace
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500">Produk Shopee</p>
            <h2 class="text-3xl font-bold">
                {{ $jumlahProduk }}
            </h2>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500">Varian Shopee</p>
            <h2 class="text-3xl font-bold">
                {{ $jumlahVarian }}
            </h2>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500">SKU Termapping</p>
            <h2 class="text-3xl font-bold">
                {{ $jumlahMapping }}
            </h2>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500">
                Sinkronisasi Terakhir
            </p>

            <h2 class="font-bold">
                {{ $lastSync?->sync_at?->format('d-m-Y H:i') ?? '-' }}
            </h2>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4">
            Shopee Integration
        </h2>

        <div class="flex flex-wrap gap-4">

            <a href="{{ route('shopee.auth') }}" class="bg-orange-500 text-white px-4 py-2 rounded">

                Hubungkan Shopee

            </a>

            <a href="{{ route('marketplace.sync-products') }}" class="bg-indigo-500 text-white px-4 py-2 rounded">
                Sinkron Produk
            </a>

            <form action="{{ route('marketplace.sync-models') }}" method="POST">

                @csrf

                <button class="bg-purple-500 text-white px-4 py-2 rounded">

                    Sinkron Variasi

                </button>

            </form>

            <a href="{{ url('/marketplace/products') }}" class="bg-blue-500 text-white px-4 py-2 rounded">

                Produk Shopee

            </a>

            <a href="{{ url('/marketplace/mapping') }}" class="bg-yellow-500 text-white px-4 py-2 rounded">

                Mapping SKU

            </a>

            <form action="{{ route('marketplace.sync-stock') }}" method="POST">

                @csrf

                <button class="bg-green-500 text-white px-4 py-2 rounded">

                    Sinkronisasi Stok

                </button>

            </form>

        </div>

    </div>

@endsection