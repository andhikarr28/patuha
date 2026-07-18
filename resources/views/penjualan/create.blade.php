@extends('layouts.app')

@section('content')

<div class="max-w-[1800px] mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                POS Penjualan
            </h1>

            <p class="text-slate-500 mt-1">
                Pilih produk dan selesaikan transaksi penjualan.
            </p>
        </div>

        <a href="{{ route('penjualan.index') }}"
           class="px-4 py-2.5 border border-slate-300
                  rounded-xl text-slate-600
                  hover:bg-slate-50 transition">

            Riwayat Penjualan

        </a>

    </div>


    {{-- ERROR VALIDATION --}}
    @if ($errors->any())

        <div class="mb-5 bg-red-50 border border-red-200
                    text-red-700 rounded-xl p-4">

            <p class="font-semibold mb-2">
                Transaksi tidak dapat diproses:
            </p>

            <ul class="list-disc ml-5 text-sm space-y-1">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        id="formPenjualan"
        action="{{ route('penjualan.store') }}"
        method="POST"
    >

        @csrf


        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">


            {{-- ========================================================= --}}
            {{-- KIRI : DAFTAR PRODUK --}}
            {{-- ========================================================= --}}

            <div class="xl:col-span-8 space-y-5">


                {{-- SEARCH + FILTER --}}
                <div class="bg-white rounded-2xl shadow-sm
                            border border-slate-200 p-5">

                    <div class="flex flex-col md:flex-row gap-3">

                        {{-- SEARCH --}}
                        <div class="relative flex-1">

                            <div class="absolute inset-y-0 left-0
                                        pl-4 flex items-center
                                        pointer-events-none">

                                <svg
                                    class="w-5 h-5 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m21 21-4.35-4.35m2.1-5.4
                                           a7.5 7.5 0 1 1-15 0
                                           7.5 7.5 0 0 1 15 0Z"
                                    />

                                </svg>

                            </div>

                            <input
                                type="text"
                                id="searchProduk"
                                placeholder="Cari barang, SKU, warna, ukuran..."
                                autocomplete="off"
                                class="w-full pl-12 pr-4 py-3
                                       border border-slate-300
                                       rounded-xl
                                       focus:ring-2
                                       focus:ring-blue-500
                                       focus:border-blue-500
                                       outline-none"
                            >

                        </div>


                        {{-- RESET --}}
                        <button
                            type="button"
                            id="resetSearch"
                            class="px-5 py-3
                                   border border-slate-300
                                   rounded-xl
                                   font-medium
                                   text-slate-600
                                   hover:bg-slate-50
                                   transition"
                        >

                            Reset

                        </button>

                    </div>


                    {{-- CATEGORY FILTER --}}
                    <div
                        id="categoryFilters"
                        class="flex gap-2 mt-4 overflow-x-auto pb-1"
                    >

                        <button
                            type="button"
                            data-category="all"
                            class="category-btn
                                   whitespace-nowrap
                                   px-4 py-2
                                   rounded-lg
                                   text-sm font-semibold
                                   bg-blue-600 text-white"
                        >

                            Semua

                        </button>


                        @php

                            $kategoriList = $varian
                                ->map(fn($v) => $v->barang?->kategori)
                                ->filter()
                                ->unique('id')
                                ->values();

                        @endphp


                        @foreach($kategoriList as $kategori)

                            <button
                                type="button"
                                data-category="{{ $kategori->id }}"
                                class="category-btn
                                       whitespace-nowrap
                                       px-4 py-2
                                       rounded-lg
                                       text-sm font-medium
                                       text-slate-600
                                       hover:bg-slate-100
                                       transition"
                            >

                                {{ $kategori->nama_kategori }}

                            </button>

                        @endforeach

                    </div>

                </div>



                {{-- PRODUCT GRID --}}
                <div class="bg-white rounded-2xl shadow-sm
                            border border-slate-200 p-5">

                    <div class="flex justify-between items-center mb-5">

                        <div>

                            <h2 class="text-lg font-bold text-slate-900">
                                Daftar Produk
                            </h2>

                            <p
                                id="productCounter"
                                class="text-sm text-slate-500 mt-1"
                            >

                                Pilih varian untuk menambah ke keranjang

                            </p>

                        </div>

                    </div>


                    <div
                        id="productGrid"
                        class="grid grid-cols-1
                               sm:grid-cols-2
                               lg:grid-cols-3
                               gap-4"
                    >

                        @forelse($varian as $item)

                            @php

                                $namaBarang =
                                    $item->barang?->nama_barang
                                    ?? 'Barang';

                                $kategoriId =
                                    $item->barang?->kategori_id
                                    ?? '';

                                $namaLengkap =
                                    $namaBarang
                                    . ' '
                                    . ($item->warna ?? '')
                                    . ' '
                                    . ($item->ukuran ?? '');

                                /*
                                |--------------------------------------------------------------------------
                                | FOTO
                                |--------------------------------------------------------------------------
                                | Sesuaikan jika kolom/path foto kamu berbeda.
                                */

                                $foto =
                                    $item->barang?->foto
                                    ? asset('storage/' . $item->barang->foto)
                                    : null;

                            @endphp


                            <button
                                type="button"

                                class="product-card
                                       text-left
                                       border border-slate-200
                                       rounded-xl
                                       p-4
                                       hover:border-blue-500
                                       hover:shadow-md
                                       transition
                                       disabled:opacity-50
                                       disabled:cursor-not-allowed"

                                data-id="{{ $item->id }}"

                                data-name="{{ strtolower($namaLengkap . ' ' . ($item->sku ?? '')) }}"

                                data-category="{{ $kategoriId }}"

                                data-nama="{{ $namaBarang }} - {{ $item->warna }} - {{ $item->ukuran }}"

                                data-harga="{{ (int) $item->harga_jual }}"

                                data-stok="{{ (int) $item->stok }}"

                                data-sku="{{ $item->sku ?? '-' }}"

                                {{ $item->stok <= 0 ? 'disabled' : '' }}
                            >

                                <div class="flex gap-4">


                                    {{-- FOTO --}}
                                    <div class="w-24 h-24
                                                flex-shrink-0
                                                rounded-xl
                                                bg-slate-100
                                                overflow-hidden
                                                flex items-center
                                                justify-center">

                                        @if($foto)

                                            <img
                                                src="{{ $foto }}"
                                                alt="{{ $namaBarang }}"
                                                class="w-full h-full object-cover"
                                            >

                                        @else

                                            <div class="text-center text-slate-400">

                                                <svg
                                                    class="w-8 h-8 mx-auto"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="m2.25 15.75
                                                           5.159-5.159
                                                           a2.25 2.25 0 0 1
                                                           3.182 0
                                                           l5.159 5.159
                                                           m-1.5-1.5
                                                           1.409-1.409
                                                           a2.25 2.25 0 0 1
                                                           3.182 0
                                                           l2.909 2.909
                                                           m-18 3.75
                                                           h16.5
                                                           a1.5 1.5 0 0 0
                                                           1.5-1.5V6
                                                           a1.5 1.5 0 0 0
                                                           -1.5-1.5H3
                                                           A1.5 1.5 0 0 0
                                                           1.5 6v12
                                                           a1.5 1.5 0 0 0
                                                           1.5 1.5Z"
                                                    />

                                                </svg>

                                                <span class="text-xs">
                                                    No Image
                                                </span>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- INFO --}}
                                    <div class="min-w-0 flex-1">

                                        <h3
                                            class="font-bold
                                                   text-slate-900
                                                   leading-snug"
                                        >

                                            {{ $namaBarang }}

                                        </h3>


                                        <p class="text-sm text-slate-600 mt-1">

                                            {{ $item->warna ?: '-' }}

                                            @if($item->ukuran)

                                                / {{ $item->ukuran }}

                                            @endif

                                        </p>


                                        <p class="text-xs text-slate-400 mt-1 truncate">

                                            SKU:
                                            {{ $item->sku ?? '-' }}

                                        </p>


                                        <div
                                            class="flex
                                                   items-end
                                                   justify-between
                                                   gap-2
                                                   mt-3"
                                        >

                                            @if($item->stok > 0)

                                                <span
                                                    class="text-xs
                                                           font-semibold
                                                           px-2 py-1
                                                           bg-green-100
                                                           text-green-700
                                                           rounded-md"
                                                >

                                                    Stok:
                                                    {{ $item->stok }}

                                                </span>

                                            @else

                                                <span
                                                    class="text-xs
                                                           font-semibold
                                                           px-2 py-1
                                                           bg-red-100
                                                           text-red-700
                                                           rounded-md"
                                                >

                                                    Habis

                                                </span>

                                            @endif


                                            <span
                                                class="font-bold
                                                       text-blue-600"
                                            >

                                                Rp
                                                {{ number_format(
                                                    $item->harga_jual,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </button>


                        @empty

                            <div
                                class="col-span-full
                                       text-center
                                       py-16
                                       text-slate-500"
                            >

                                Belum ada varian barang.

                            </div>

                        @endforelse

                    </div>


                    {{-- EMPTY SEARCH --}}
                    <div
                        id="emptyProducts"
                        class="hidden text-center py-16"
                    >

                        <p class="font-semibold text-slate-700">
                            Produk tidak ditemukan
                        </p>

                        <p class="text-sm text-slate-500 mt-1">
                            Coba gunakan kata pencarian lain.
                        </p>

                    </div>

                </div>



                {{-- TRANSACTION INFO --}}
                <div
                    class="bg-white
                           border border-slate-200
                           rounded-2xl
                           shadow-sm
                           p-5"
                >

                    <div
                        class="grid
                               grid-cols-1
                               md:grid-cols-3
                               gap-5"
                    >


                        {{-- NOTA --}}
                        <div>

                            <p
                                class="text-xs
                                       uppercase
                                       tracking-wide
                                       text-slate-400
                                       mb-1"
                            >

                                No Nota

                            </p>

                            <input
                                type="text"
                                name="no_nota"
                                value="PJ-{{ now()->format('YmdHis') }}"
                                readonly
                                class="w-full
                                       font-semibold
                                       bg-transparent
                                       outline-none"
                            >

                        </div>


                        {{-- TANGGAL --}}
                        <div>

                            <p
                                class="text-xs
                                       uppercase
                                       tracking-wide
                                       text-slate-400
                                       mb-1"
                            >

                                Tanggal

                            </p>

                            <input
                                type="date"
                                name="tanggal_penjualan"
                                value="{{ date('Y-m-d') }}"
                                class="w-full
                                       font-semibold
                                       bg-transparent
                                       outline-none"
                            >

                        </div>


                        {{-- CHANNEL --}}
                        <div>

                            <p
                                class="text-xs
                                       uppercase
                                       tracking-wide
                                       text-slate-400
                                       mb-1"
                            >

                                Channel

                            </p>

                            <select
                                name="channel"
                                class="w-full
                                       font-semibold
                                       bg-transparent
                                       outline-none"
                            >

                                <option value="offline">
                                    Offline
                                </option>

                                <option value="shopee">
                                    Shopee
                                </option>

                                <option value="tokopedia">
                                    Tokopedia
                                </option>

                                <option value="tiktok">
                                    TikTok Shop
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>



            {{-- ========================================================= --}}
            {{-- KANAN : KERANJANG --}}
            {{-- ========================================================= --}}

            <div class="xl:col-span-4">

                <div
                    class="bg-white
                           rounded-2xl
                           shadow-sm
                           border border-slate-200
                           overflow-hidden
                           xl:sticky
                           xl:top-6"
                >


                    {{-- CART HEADER --}}
                    <div
                        class="p-5
                               border-b
                               border-slate-200
                               flex
                               justify-between
                               items-center"
                    >

                        <div>

                            <h2
                                class="text-xl
                                       font-bold
                                       text-slate-900"
                            >

                                Keranjang

                                <span
                                    id="cartCount"
                                    class="text-slate-400"
                                >

                                    (0)

                                </span>

                            </h2>

                        </div>


                        <button
                            type="button"
                            id="btnClearCart"
                            class="text-sm
                                   text-red-500
                                   hover:text-red-700
                                   font-medium"
                        >

                            Bersihkan

                        </button>

                    </div>



                    {{-- CART ITEMS --}}
                    <div
                        id="cartItems"
                        class="p-5
                               max-h-[430px]
                               overflow-y-auto"
                    >

                        <div
                            id="emptyCart"
                            class="text-center py-12"
                        >

                            <div
                                class="w-14 h-14
                                       mx-auto
                                       rounded-full
                                       bg-slate-100
                                       flex
                                       items-center
                                       justify-center
                                       mb-3"
                            >

                                <svg
                                    class="w-7 h-7 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M2.25 3h1.386
                                           c.51 0 .955.343
                                           1.087.835
                                           l.383 1.437
                                           M7.5 14.25
                                           a3 3 0 0 0-3 3
                                           h15.75
                                           m-12.75-3
                                           h11.218
                                           c1.121-2.3
                                           2.1-4.684
                                           2.924-7.138
                                           a60.114 60.114
                                           0 0 0-16.536-1.84
                                           M7.5 14.25
                                           5.106 2.852
                                           M6 20.25
                                           h.008v.008H6v-.008Zm12
                                           0h.008v.008H18v-.008Z"
                                    />

                                </svg>

                            </div>

                            <p class="font-medium text-slate-600">
                                Keranjang masih kosong
                            </p>

                            <p class="text-sm text-slate-400 mt-1">
                                Klik produk untuk menambahkan.
                            </p>

                        </div>

                    </div>



                    {{-- TOTAL --}}
                    <div
                        class="border-t
                               border-slate-200
                               p-5"
                    >


                        <div
                            class="flex
                                   justify-between
                                   items-center
                                   text-sm
                                   mb-3"
                        >

                            <span class="text-slate-500">
                                Subtotal
                            </span>

                            <span
                                id="subtotal"
                                class="font-semibold"
                            >

                                Rp 0

                            </span>

                        </div>


                        <div
                            class="flex
                                   justify-between
                                   items-center
                                   pt-4
                                   border-t
                                   border-dashed
                                   border-slate-300"
                        >

                            <span
                                class="text-xl
                                       font-bold
                                       text-slate-900"
                            >

                                Total

                            </span>

                            <span
                                id="grandTotal"
                                class="text-2xl
                                       font-bold
                                       text-blue-600"
                            >

                                Rp 0

                            </span>

                        </div>



                        {{-- PAYMENT --}}
                        <div class="mt-5">

                            <label
                                class="block
                                       text-sm
                                       font-semibold
                                       text-slate-700
                                       mb-2"
                            >

                                Metode Pembayaran

                            </label>

                            <select
                                name="metode_pembayaran"
                                class="w-full
                                       border
                                       border-slate-300
                                       rounded-xl
                                       p-3
                                       focus:ring-2
                                       focus:ring-blue-500
                                       outline-none"
                            >

                                <option value="cash">
                                    Cash
                                </option>

                                <option value="qris">
                                    QRIS
                                </option>

                                <option value="transfer">
                                    Transfer
                                </option>

                                <option value="debit">
                                    Debit
                                </option>

                            </select>

                        </div>



                        {{-- SUBMIT --}}
                        <button
                            id="btnBayar"
                            type="submit"
                            disabled
                            class="w-full
                                   mt-5
                                   bg-green-600
                                   hover:bg-green-700
                                   disabled:bg-slate-300
                                   disabled:cursor-not-allowed
                                   text-white
                                   font-bold
                                   py-4
                                   px-5
                                   rounded-xl
                                   transition"
                        >

                            BAYAR
                            <span id="buttonTotal">
                                (Rp 0)
                            </span>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>



<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let cart = [];

    let activeCategory = 'all';


    const form =
        document.getElementById('formPenjualan');

    const cartItems =
        document.getElementById('cartItems');

    const cartCount =
        document.getElementById('cartCount');

    const subtotalElement =
        document.getElementById('subtotal');

    const grandTotal =
        document.getElementById('grandTotal');

    const buttonTotal =
        document.getElementById('buttonTotal');

    const btnBayar =
        document.getElementById('btnBayar');

    const btnClearCart =
        document.getElementById('btnClearCart');

    const searchProduk =
        document.getElementById('searchProduk');

    const resetSearch =
        document.getElementById('resetSearch');

    const emptyProducts =
        document.getElementById('emptyProducts');

    const productCounter =
        document.getElementById('productCounter');



    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    function rupiah(value) {

        return 'Rp ' +
            Number(value).toLocaleString('id-ID');

    }



    /*
    |--------------------------------------------------------------------------
    | TAMBAH PRODUK DARI CARD
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.product-card')
        .forEach(function (card) {

            card.addEventListener(
                'click',
                function () {

                    if (card.disabled) {
                        return;
                    }

                    const varianId =
                        card.dataset.id;

                    const nama =
                        card.dataset.nama;

                    const harga =
                        Number(card.dataset.harga);

                    const stok =
                        Number(card.dataset.stok);

                    const sku =
                        card.dataset.sku;


                    const existing =
                        cart.find(
                            item =>
                                String(item.varian_id)
                                === String(varianId)
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA SUDAH ADA DI CART
                    |--------------------------------------------------------------------------
                    */

                    if (existing) {

                        if (existing.qty >= stok) {

                            alert(
                                'Jumlah tidak dapat melebihi stok tersedia.'
                            );

                            return;
                        }

                        existing.qty++;

                        existing.subtotal =
                            existing.qty *
                            existing.harga;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM BARU
                    |--------------------------------------------------------------------------
                    */

                    else {

                        cart.push({

                            varian_id:
                                varianId,

                            nama:
                                nama,

                            sku:
                                sku,

                            qty:
                                1,

                            harga:
                                harga,

                            stok:
                                stok,

                            subtotal:
                                harga

                        });

                    }


                    renderCart();

                }
            );

        });



    /*
    |--------------------------------------------------------------------------
    | RENDER CART
    |--------------------------------------------------------------------------
    */

    function renderCart() {

        /*
        |--------------------------------------------------------------------------
        | HAPUS INPUT HIDDEN LAMA
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.cart-hidden')
            .forEach(
                element =>
                    element.remove()
            );


        /*
        |--------------------------------------------------------------------------
        | EMPTY CART
        |--------------------------------------------------------------------------
        */

        if (cart.length === 0) {

            cartItems.innerHTML = `

                <div class="text-center py-12">

                    <div
                        class="
                            w-14 h-14
                            mx-auto
                            rounded-full
                            bg-slate-100
                            flex
                            items-center
                            justify-center
                            mb-3
                        "
                    >

                        <svg
                            class="w-7 h-7 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="
                                    M2.25 3h1.386
                                    c.51 0 .955.343
                                    1.087.835
                                    l.383 1.437

                                    M7.5 14.25
                                    a3 3 0 0 0-3 3
                                    h15.75

                                    m-12.75-3
                                    h11.218

                                    c1.121-2.3
                                    2.1-4.684
                                    2.924-7.138
                                "
                            />

                        </svg>

                    </div>

                    <p class="font-medium text-slate-600">
                        Keranjang masih kosong
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        Klik produk untuk menambahkan.
                    </p>

                </div>

            `;

        }

        else {

            let html = '';


            cart.forEach(
                function (item, index) {

                    html += `

                        <div
                            class="
                                py-4
                                border-b
                                border-slate-100
                                last:border-0
                            "
                        >

                            <div
                                class="
                                    flex
                                    justify-between
                                    gap-3
                                "
                            >

                                <div class="min-w-0">

                                    <h3
                                        class="
                                            font-semibold
                                            text-slate-900
                                            text-sm
                                        "
                                    >

                                        ${item.nama}

                                    </h3>

                                    <p
                                        class="
                                            text-xs
                                            text-slate-400
                                            mt-1
                                        "
                                    >

                                        SKU:
                                        ${item.sku}

                                    </p>

                                    <p
                                        class="
                                            text-sm
                                            font-semibold
                                            text-slate-700
                                            mt-2
                                        "
                                    >

                                        ${rupiah(item.harga)}

                                    </p>

                                </div>


                                <button
                                    type="button"

                                    onclick="
                                        hapusItem(${index})
                                    "

                                    class="
                                        text-red-500
                                        hover:text-red-700
                                        text-sm
                                        self-start
                                    "
                                >

                                    Hapus

                                </button>

                            </div>


                            <div
                                class="
                                    flex
                                    justify-between
                                    items-center
                                    mt-3
                                "
                            >

                                <div
                                    class="
                                        inline-flex
                                        items-center
                                        border
                                        border-slate-300
                                        rounded-lg
                                        overflow-hidden
                                    "
                                >

                                    <button
                                        type="button"

                                        onclick="
                                            kurangiQty(${index})
                                        "

                                        class="
                                            w-9 h-9
                                            hover:bg-slate-100
                                        "
                                    >

                                        −

                                    </button>


                                    <span
                                        class="
                                            w-10
                                            text-center
                                            text-sm
                                            font-semibold
                                        "
                                    >

                                        ${item.qty}

                                    </span>


                                    <button
                                        type="button"

                                        onclick="
                                            tambahQty(${index})
                                        "

                                        class="
                                            w-9 h-9
                                            hover:bg-slate-100
                                        "
                                    >

                                        +

                                    </button>

                                </div>


                                <div class="text-right">

                                    <p
                                        class="
                                            font-bold
                                            text-slate-900
                                        "
                                    >

                                        ${rupiah(
                                            item.subtotal
                                        )}

                                    </p>

                                    <p
                                        class="
                                            text-xs
                                            text-slate-400
                                        "
                                    >

                                        Stok:
                                        ${item.stok}

                                    </p>

                                </div>

                            </div>

                        </div>

                    `;


                    /*
                    |--------------------------------------------------------------------------
                    | INPUT UNTUK CONTROLLER LAMA
                    |--------------------------------------------------------------------------
                    */

                    form.insertAdjacentHTML(

                        'beforeend',

                        `

                        <input
                            class="cart-hidden"
                            type="hidden"
                            name="cart[${index}][varian_id]"
                            value="${item.varian_id}"
                        >

                        <input
                            class="cart-hidden"
                            type="hidden"
                            name="cart[${index}][qty]"
                            value="${item.qty}"
                        >

                        `

                    );

                }
            );


            cartItems.innerHTML =
                html;

        }



        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        const total =
            cart.reduce(

                (sum, item) =>
                    sum +
                    item.subtotal,

                0

            );


        const totalQty =
            cart.reduce(

                (sum, item) =>
                    sum +
                    item.qty,

                0

            );


        cartCount.textContent =
            '(' + totalQty + ')';

        subtotalElement.textContent =
            rupiah(total);

        grandTotal.textContent =
            rupiah(total);

        buttonTotal.textContent =
            '(' +
            rupiah(total) +
            ')';


        btnBayar.disabled =
            cart.length === 0;

    }



    /*
    |--------------------------------------------------------------------------
    | QUANTITY
    |--------------------------------------------------------------------------
    */

    window.tambahQty =
        function (index) {

            const item =
                cart[index];


            if (item.qty >= item.stok) {

                alert(
                    'Jumlah tidak dapat melebihi stok tersedia.'
                );

                return;

            }


            item.qty++;

            item.subtotal =
                item.qty *
                item.harga;


            renderCart();

        };


    window.kurangiQty =
        function (index) {

            const item =
                cart[index];


            if (item.qty <= 1) {

                cart.splice(
                    index,
                    1
                );

            }

            else {

                item.qty--;

                item.subtotal =
                    item.qty *
                    item.harga;

            }


            renderCart();

        };


    window.hapusItem =
        function (index) {

            cart.splice(
                index,
                1
            );


            renderCart();

        };



    /*
    |--------------------------------------------------------------------------
    | CLEAR CART
    |--------------------------------------------------------------------------
    */

    btnClearCart.addEventListener(
        'click',
        function () {

            if (cart.length === 0) {
                return;
            }


            if (
                confirm(
                    'Kosongkan seluruh keranjang?'
                )
            ) {

                cart = [];

                renderCart();

            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | SEARCH + CATEGORY
    |--------------------------------------------------------------------------
    */

    function filterProducts() {

        const keyword =
            searchProduk
                .value
                .toLowerCase()
                .trim();


        let visible =
            0;


        document
            .querySelectorAll(
                '.product-card'
            )
            .forEach(
                function (card) {

                    const name =
                        card.dataset.name;

                    const category =
                        card.dataset.category;


                    const matchSearch =
                        name.includes(
                            keyword
                        );


                    const matchCategory =

                        activeCategory
                        === 'all'

                        ||

                        String(category)
                        ===
                        String(activeCategory);


                    const show =
                        matchSearch
                        &&
                        matchCategory;


                    card.classList.toggle(
                        'hidden',
                        !show
                    );


                    if (show) {
                        visible++;
                    }

                }
            );


        emptyProducts.classList.toggle(
            'hidden',
            visible !== 0
        );


        productCounter.textContent =

            visible +
            ' varian tersedia';

    }



    searchProduk.addEventListener(
        'input',
        filterProducts
    );


    resetSearch.addEventListener(
        'click',
        function () {

            searchProduk.value =
                '';

            activeCategory =
                'all';


            document
                .querySelectorAll(
                    '.category-btn'
                )
                .forEach(
                    function (button) {

                        button.classList.remove(
                            'bg-blue-600',
                            'text-white'
                        );

                        button.classList.add(
                            'text-slate-600'
                        );

                    }
                );


            const allButton =
                document.querySelector(
                    '[data-category="all"]'
                );


            allButton.classList.add(
                'bg-blue-600',
                'text-white'
            );


            filterProducts();

        }
    );



    document
        .querySelectorAll(
            '.category-btn'
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        activeCategory =
                            button.dataset.category;


                        document
                            .querySelectorAll(
                                '.category-btn'
                            )
                            .forEach(
                                function (btn) {

                                    btn.classList.remove(
                                        'bg-blue-600',
                                        'text-white'
                                    );

                                    btn.classList.add(
                                        'text-slate-600'
                                    );

                                }
                            );


                        button.classList.remove(
                            'text-slate-600'
                        );

                        button.classList.add(
                            'bg-blue-600',
                            'text-white'
                        );


                        filterProducts();

                    }
                );

            }
        );



    /*
    |--------------------------------------------------------------------------
    | VALIDASI SEBELUM SUBMIT
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function (event) {

            if (cart.length === 0) {

                event.preventDefault();

                alert(
                    'Keranjang masih kosong.'
                );

                return;

            }


            btnBayar.disabled =
                true;

            btnBayar.innerHTML =
                'Memproses transaksi...';

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    filterProducts();

    renderCart();

});

</script>

@endsection