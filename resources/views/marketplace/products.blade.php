@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>

            <a href="{{ route('marketplace.index') }}"
               class="inline-flex items-center gap-2
                      text-sm text-slate-500 hover:text-blue-600
                      transition mb-2">

                ← Kembali ke Marketplace

            </a>

            <h1 class="text-3xl font-bold text-slate-900">
                Produk Shopee
            </h1>

            <p class="text-slate-500 mt-1">
                Daftar produk yang telah disinkronkan dari Shopee ke sistem.
            </p>

        </div>


        {{-- SINKRON PRODUK --}}
        <form action="{{ route('marketplace.sync.products') }}"
              method="POST">

            @csrf

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2
                           bg-blue-600 hover:bg-blue-700
                           text-white font-semibold
                           px-5 py-3 rounded-xl transition">

                🔄 Sinkron Produk

            </button>

        </form>

    </div>


    {{-- ============================================================
        SUMMARY
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- TOTAL PRODUK --}}
        <div class="bg-white border border-slate-200
                    rounded-2xl p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Produk
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-1">
                        {{ $products->count() }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        produk dari Shopee
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl
                            bg-orange-50
                            flex items-center justify-center
                            text-xl">

                    📦

                </div>

            </div>

        </div>


        {{-- PRODUK AKTIF --}}
        <div class="bg-white border border-slate-200
                    rounded-2xl p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Produk Aktif
                    </p>

                    <p class="text-3xl font-bold text-green-600 mt-1">

                        {{
                            $products->filter(function ($product) {

                                return in_array(
                                    strtolower($product->status ?? ''),
                                    [
                                        'normal',
                                        'active',
                                        'aktif'
                                    ]
                                );

                            })->count()
                        }}

                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        tersedia di marketplace
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl
                            bg-green-50
                            flex items-center justify-center
                            text-xl">

                    ✓

                </div>

            </div>

        </div>


        {{-- SUMBER DATA --}}
        <div class="bg-white border border-slate-200
                    rounded-2xl p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Sumber Data
                    </p>

                    <p class="text-lg font-bold text-slate-900 mt-2">
                        Shopee
                    </p>

                    <p class="text-xs text-green-600 font-medium mt-1">
                        ● Data tersinkron
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl
                            bg-orange-50
                            flex items-center justify-center
                            text-xl">

                    🛍️

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        INFO
    ============================================================ --}}
    <div class="bg-blue-50 border border-blue-200
                rounded-2xl p-5">

        <div class="flex items-start gap-4">

            <div class="w-11 h-11 rounded-xl
                        bg-blue-100
                        flex items-center justify-center
                        flex-shrink-0">

                ℹ️

            </div>

            <div>

                <h3 class="font-bold text-blue-900">
                    Data Produk Marketplace
                </h3>

                <p class="text-sm text-blue-700 mt-1 leading-relaxed">

                    Data pada halaman ini berasal dari Shopee.
                    Gunakan fitur sinkronisasi untuk memperbarui produk
                    apabila terdapat perubahan pada marketplace.

                </p>

            </div>

        </div>

    </div>


    {{-- ============================================================
        PRODUCT LIST
    ============================================================ --}}
    <div class="bg-white border border-slate-200
                rounded-2xl overflow-hidden">

        {{-- TABLE HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200">

            <div class="flex flex-col md:flex-row
                        md:items-center md:justify-between gap-4">

                <div>

                    <h2 class="text-lg font-bold text-slate-900">
                        Daftar Produk
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Produk yang berhasil diambil dari akun Shopee terhubung.
                    </p>

                </div>


                {{-- SEARCH --}}
                <div class="relative">

                    <span class="absolute left-4 top-1/2
                                 -translate-y-1/2
                                 text-slate-400">

                        🔍

                    </span>

                    <input type="text"
                           id="productSearch"
                           placeholder="Cari produk atau ID..."

                           class="w-full md:w-72
                                  border border-slate-300
                                  rounded-xl
                                  pl-11 pr-4 py-3
                                  text-sm
                                  focus:ring-2 focus:ring-blue-500
                                  focus:border-blue-500">

                </div>

            </div>

        </div>


        @if($products->count())

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50
                                  border-b border-slate-200">

                        <tr>

                            <th class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500 uppercase
                                       tracking-wide">

                                Produk

                            </th>

                            <th class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500 uppercase
                                       tracking-wide">

                                ID Shopee

                            </th>

                            <th class="px-6 py-4
                                       text-center
                                       text-xs font-semibold
                                       text-slate-500 uppercase
                                       tracking-wide">

                                Berat

                            </th>

                            <th class="px-6 py-4
                                       text-center
                                       text-xs font-semibold
                                       text-slate-500 uppercase
                                       tracking-wide">

                                Status

                            </th>

                        </tr>

                    </thead>


                    <tbody id="productTable">

                        @foreach($products as $product)

                            @php

                                $status = strtolower(
                                    $product->status ?? ''
                                );

                                $isActive = in_array(
                                    $status,
                                    [
                                        'normal',
                                        'active',
                                        'aktif'
                                    ]
                                );

                            @endphp


                            <tr class="product-row
                                       border-b border-slate-100
                                       last:border-b-0
                                       hover:bg-slate-50
                                       transition">

                                {{-- PRODUK --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-4">

                                        <div class="w-12 h-12
                                                    rounded-xl
                                                    bg-orange-50
                                                    flex items-center
                                                    justify-center
                                                    text-xl
                                                    flex-shrink-0">

                                            📦

                                        </div>


                                        <div>

                                            <p class="product-name
                                                      font-semibold
                                                      text-slate-900">

                                                {{
                                                    $product->nama_produk
                                                    ?: 'Produk Tanpa Nama'
                                                }}

                                            </p>

                                            <p class="text-xs
                                                      text-slate-400
                                                      mt-1">

                                                Produk Shopee

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- ID --}}
                                <td class="px-6 py-5">

                                    <span class="product-id
                                                 inline-flex
                                                 bg-slate-100
                                                 text-slate-700
                                                 font-mono text-xs
                                                 px-3 py-2
                                                 rounded-lg">

                                        {{
                                            $product->external_product_id
                                            ?: '-'
                                        }}

                                    </span>

                                </td>


                                {{-- BERAT --}}
                                <td class="px-6 py-5 text-center">

                                    @if($product->berat)

                                        <span class="font-semibold
                                                     text-slate-700">

                                            {{ number_format(
                                                $product->berat,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </span>

                                        <span class="text-sm
                                                     text-slate-400">

                                            gram

                                        </span>

                                    @else

                                        <span class="text-slate-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}
                                <td class="px-6 py-5 text-center">

                                    @if($isActive)

                                        <span class="inline-flex
                                                     items-center gap-2
                                                     bg-green-50
                                                     text-green-700
                                                     border border-green-200
                                                     text-xs font-semibold
                                                     px-3 py-1.5
                                                     rounded-full">

                                            <span class="w-2 h-2
                                                         rounded-full
                                                         bg-green-500">
                                            </span>

                                            Aktif

                                        </span>

                                    @elseif($product->status)

                                        <span class="inline-flex
                                                     items-center gap-2
                                                     bg-slate-100
                                                     text-slate-600
                                                     text-xs font-semibold
                                                     px-3 py-1.5
                                                     rounded-full">

                                            <span class="w-2 h-2
                                                         rounded-full
                                                         bg-slate-400">
                                            </span>

                                            {{ ucfirst(
                                                strtolower(
                                                    $product->status
                                                )
                                            ) }}

                                        </span>

                                    @else

                                        <span class="inline-flex
                                                     bg-slate-100
                                                     text-slate-500
                                                     text-xs font-semibold
                                                     px-3 py-1.5
                                                     rounded-full">

                                            Tidak diketahui

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- NO SEARCH RESULT --}}
            <div id="noSearchResult"
                 class="hidden py-16 text-center">

                <div class="text-4xl mb-3">
                    🔍
                </div>

                <h3 class="font-bold text-slate-900">
                    Produk Tidak Ditemukan
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Coba gunakan nama produk atau ID Shopee yang berbeda.
                </p>

            </div>


            {{-- FOOTER --}}
            <div class="px-6 py-4
                        bg-slate-50
                        border-t border-slate-200">

                <p class="text-sm text-slate-500">

                    Menampilkan

                    <span class="font-semibold text-slate-700">
                        {{ $products->count() }}
                    </span>

                    produk Shopee.

                </p>

            </div>

        @else

            {{-- EMPTY STATE --}}
            <div class="py-20 text-center">

                <div class="w-16 h-16
                            mx-auto
                            bg-orange-50
                            rounded-2xl
                            flex items-center
                            justify-center
                            text-3xl mb-4">

                    📦

                </div>

                <h3 class="text-lg font-bold text-slate-900">
                    Belum Ada Produk Shopee
                </h3>

                <p class="text-sm text-slate-500
                          mt-2 max-w-md mx-auto">

                    Produk belum pernah disinkronkan.
                    Lakukan sinkronisasi untuk mengambil data
                    produk dari Shopee.

                </p>


                <form action="{{ route('marketplace.sync.products') }}"
                      method="POST"
                      class="mt-6">

                    @csrf

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700
                                   text-white font-semibold
                                   px-5 py-3
                                   rounded-xl transition">

                        🔄 Sinkron Produk Sekarang

                    </button>

                </form>

            </div>

        @endif

    </div>

</div>


{{-- ============================================================
    SEARCH SCRIPT
============================================================ --}}
<script>

    const productSearch =
        document.getElementById('productSearch');

    if (productSearch) {

        productSearch.addEventListener('input', function () {

            const keyword =
                this.value.toLowerCase().trim();

            const rows =
                document.querySelectorAll('.product-row');

            let visible = 0;

            rows.forEach(row => {

                const name =
                    row.querySelector('.product-name')
                        ?.textContent
                        .toLowerCase() || '';

                const id =
                    row.querySelector('.product-id')
                        ?.textContent
                        .toLowerCase() || '';

                const match =
                    name.includes(keyword)
                    ||
                    id.includes(keyword);

                row.style.display =
                    match ? '' : 'none';

                if (match) {
                    visible++;
                }

            });


            const empty =
                document.getElementById(
                    'noSearchResult'
                );

            if (empty) {

                empty.classList.toggle(
                    'hidden',
                    visible !== 0
                );

            }

        });

    }

</script>

@endsection