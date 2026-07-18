@extends('layouts.app')

@section('content')

    <div x-data="purchasePlan()" class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Buat Perencanaan Pembelian
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pilih supplier dan barang yang akan direncanakan untuk dibeli.
                </p>

            </div>

            <a href="{{ route('perencanaan-pembelian.index') }}" class="inline-flex items-center justify-center rounded-xl
                           border border-slate-300 bg-white px-5 py-3
                           text-sm font-semibold text-slate-600
                           transition hover:bg-slate-50">
                ← Kembali
            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- VALIDATION ERROR --}}
        {{-- ========================================================= --}}

        @if($errors->any())

            <div class="rounded-2xl border border-red-200
                                   bg-red-50 p-5 text-red-700">

                <h3 class="font-semibold">
                    Perencanaan belum dapat disimpan.
                </h3>

                <ul class="mt-2 list-inside list-disc text-sm">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- FORM UTAMA --}}
        {{-- ========================================================= --}}

        <form action="{{ route('perencanaan-pembelian.store') }}" method="POST" @submit="prepareSubmit">

            @csrf


            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">

                {{-- ================================================= --}}
                {{-- LEFT CONTENT --}}
                {{-- ================================================= --}}

                <div class="space-y-6 xl:col-span-8">


                    {{-- ============================================= --}}
                    {{-- INFORMASI PERENCANAAN --}}
                    {{-- ============================================= --}}

                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-5">

                            <h2 class="text-xl font-bold text-slate-800">
                                Informasi Perencanaan
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Tentukan supplier dan tanggal perencanaan pembelian.
                            </p>

                        </div>


                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- SUPPLIER --}}

                            <div>

                                <label for="supplier_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Supplier
                                    <span class="text-red-500">*</span>
                                </label>

                                <select id="supplier_id" name="supplier_id" x-model="supplierId"
                                    @change="updateSupplierName" required class="w-full rounded-xl border border-slate-300
                                               bg-white px-4 py-3 text-slate-700
                                               outline-none transition
                                               focus:border-blue-500
                                               focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Pilih Supplier
                                    </option>

                                    @foreach($supplier as $item)

                                        <option value="{{ $item->id }}" @selected(
                                            old('supplier_id') == $item->id
                                        )>
                                            {{ $item->nama_supplier }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- TANGGAL --}}

                            <div>

                                <label for="tanggal_perencanaan" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Tanggal Perencanaan
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" id="tanggal_perencanaan" name="tanggal_perencanaan" value="{{ old(
        'tanggal_perencanaan',
        now()->format('Y-m-d')
    ) }}" required class="w-full rounded-xl border border-slate-300
                                               px-4 py-3 text-slate-700
                                               outline-none transition
                                               focus:border-blue-500
                                               focus:ring-2 focus:ring-blue-100">

                            </div>

                        </div>


                        {{-- CATATAN --}}

                        <div class="mt-5">

                            <label for="catatan" class="mb-2 block text-sm font-semibold text-slate-700">
                                Catatan
                            </label>

                            <textarea id="catatan" name="catatan" rows="3"
                                placeholder="Contoh: Restock untuk persediaan bulan depan..." class="w-full resize-none rounded-xl
                                           border border-slate-300 px-4 py-3
                                           text-slate-700 outline-none transition
                                           focus:border-blue-500
                                           focus:ring-2 focus:ring-blue-100">{{ old('catatan') }}</textarea>

                        </div>

                    </div>


                    {{-- ============================================= --}}
                    {{-- SEARCH & FILTER --}}
                    {{-- ============================================= --}}

                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="flex flex-col gap-4 lg:flex-row">

                            {{-- SEARCH --}}

                            <div class="relative flex-1">

                                <div class="pointer-events-none absolute inset-y-0
                                               left-0 flex items-center pl-4
                                               text-slate-400">
                                    🔍
                                </div>

                                <input type="text" x-model="search" placeholder="Cari barang, SKU, warna, ukuran..." class="w-full rounded-xl border
                                               border-slate-300 py-3 pl-11 pr-4
                                               text-slate-700 outline-none transition
                                               focus:border-blue-500
                                               focus:ring-2 focus:ring-blue-100">

                            </div>


                            <button type="button" @click="
                                        search = '';
                                        selectedCategory = 'Semua';
                                    " class="rounded-xl border border-slate-300
                                           px-5 py-3 text-sm font-semibold
                                           text-slate-600 transition
                                           hover:bg-slate-50">
                                Reset
                            </button>

                        </div>


                        {{-- CATEGORY FILTER --}}

                        <div class="mt-5 flex flex-wrap gap-2">

                            <button type="button" @click="selectedCategory = 'Semua'" :class="
                                        selectedCategory === 'Semua'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                    " class="rounded-xl px-4 py-2
                                           text-sm font-semibold transition">
                                Semua
                            </button>


                            @php

                                $categories = $varian
                                    ->map(function ($item) {

                                        return
                                            $item->barang?->kategori?->nama_kategori;

                                    })
                                    ->filter()
                                    ->unique()
                                    ->values();

                            @endphp


                            @foreach($categories as $category)

                                <button type="button" @click="
                                                    selectedCategory =
                                                        @js($category)
                                                " :class="
                                                    selectedCategory ===
                                                    @js($category)
                                                        ? 'bg-blue-600 text-white'
                                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                                " class="rounded-xl px-4 py-2
                                                       text-sm font-semibold transition">
                                    {{ $category }}
                                </button>

                            @endforeach

                        </div>

                    </div>


                    {{-- ============================================= --}}
                    {{-- PRODUCT LIST --}}
                    {{-- ============================================= --}}

                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-5 flex items-center justify-between">

                            <div>

                                <h2 class="text-xl font-bold text-slate-800">
                                    Daftar Produk
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Pilih varian yang akan dimasukkan
                                    ke perencanaan.
                                </p>

                            </div>

                            <div class="rounded-xl bg-slate-100
                                           px-4 py-2 text-sm font-semibold
                                           text-slate-600">
                                {{ $varian->count() }} Varian
                            </div>

                        </div>


                        {{-- PRODUCT GRID --}}

                        <div class="grid grid-cols-1 gap-4
                                       md:grid-cols-2 2xl:grid-cols-3">

                            @foreach($varian as $item)

                                @php

                                    $namaBarang =
                                        $item->barang?->nama_barang
                                        ?? 'Barang';

                                    $kategori =
                                        $item->barang?->kategori?->nama_kategori
                                        ?? '';

                                    $warna =
                                        $item->warna
                                        ?? '-';

                                    $ukuran =
                                        $item->ukuran
                                        ?? '-';

                                    $sku =
                                        $item->sku
                                        ?? '-';

                                    $foto =
                                        $item->barang?->foto
                                        ?? null;

                                @endphp


                                <div x-show="
                                                                        productVisible(
                                                                            @js($namaBarang),
                                                                            @js($sku),
                                                                            @js($warna),
                                                                            @js($ukuran),
                                                                            @js($kategori)
                                                                        )
                                                                    " x-transition class="group rounded-2xl border
                                                                           border-slate-200 p-4 transition
                                                                           hover:border-blue-300
                                                                           hover:shadow-md">

                                    <div class="flex gap-4">

                                        {{-- IMAGE --}}

                                        <div class="flex h-24 w-24 shrink-0
                                                                                   items-center justify-center
                                                                                   overflow-hidden rounded-xl
                                                                                   bg-slate-100">

                                            @if($foto)

                                                                            <img src="{{ asset(
                                                    'storage/' . $foto
                                                ) }}" alt="{{ $namaBarang }}"
                                                                                class="h-full w-full
                                                                                                                                                           object-cover">

                                            @else

                                                <div class="text-center
                                                                                                   text-slate-400">

                                                    <div class="text-2xl">
                                                        🖼️
                                                    </div>

                                                    <div class="mt-1 text-xs">
                                                        No Image
                                                    </div>

                                                </div>

                                            @endif

                                        </div>


                                        {{-- PRODUCT INFO --}}

                                        <div class="min-w-0 flex-1">

                                            <h3 class="line-clamp-2
                                                                                       font-bold text-slate-800">
                                                {{ $namaBarang }}
                                            </h3>

                                            <p class="mt-1 text-sm
                                                                                       text-slate-500">
                                                {{ $warna }}
                                                /
                                                {{ $ukuran }}
                                            </p>

                                            <p class="mt-1 truncate
                                                                                       text-xs text-slate-400">
                                                SKU:
                                                {{ $sku }}
                                            </p>


                                            <div class="mt-3 flex
                                                                                       items-center justify-between
                                                                                       gap-2">

                                                <span class="rounded-lg bg-green-50
                                                                                           px-2 py-1 text-xs
                                                                                           font-semibold
                                                                                           text-green-700">
                                                    Stok:
                                                    {{ $item->stok }}
                                                </span>

                                                <button type="button" @click="addItem(
                                            {{ $item->id }},
                                            {{ Js::from($namaBarang) }},
                                            {{ Js::from($warna) }},
                                            {{ Js::from($ukuran) }},
                                            {{ Js::from($sku) }},
                                            {{ (int) $item->stok }}
                                        )" class="rounded-lg bg-blue-600
                                               px-3 py-2 text-xs
                                               font-semibold text-white
                                               transition hover:bg-blue-700">
                                                    + Tambah
                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- RIGHT SIDEBAR / CART --}}
                {{-- ================================================= --}}

                <div class="xl:col-span-4">

                    <div class="sticky top-6 overflow-hidden
                                   rounded-2xl bg-white shadow-sm">

                        {{-- HEADER --}}

                        <div class="flex items-center justify-between
                                       border-b border-slate-100 p-6">

                            <div>

                                <h2 class="text-xl font-bold text-slate-800">

                                    Rencana Pembelian

                                    <span class="text-slate-400">
                                        (<span x-text="cart.length"></span>)
                                    </span>

                                </h2>

                                <p class="mt-1 text-xs text-slate-500" x-text="
                                            supplierName
                                                ? supplierName
                                                : 'Supplier belum dipilih'
                                        "></p>

                            </div>


                            <button type="button" x-show="cart.length > 0" @click="clearCart" class="text-sm font-medium
                                           text-red-500 hover:text-red-600">
                                Bersihkan
                            </button>

                        </div>


                        {{-- ========================================= --}}
                        {{-- EMPTY CART --}}
                        {{-- ========================================= --}}

                        <div x-show="cart.length === 0" class="px-6 py-16 text-center">

                            <div class="mx-auto flex h-16 w-16
                                           items-center justify-center
                                           rounded-full bg-slate-100 text-2xl">
                                📋
                            </div>

                            <h3 class="mt-4 font-bold text-slate-700">
                                Belum Ada Barang
                            </h3>

                            <p class="mt-2 text-sm leading-relaxed
                                           text-slate-500">
                                Pilih produk di sebelah kiri
                                untuk menambahkannya ke perencanaan.
                            </p>

                        </div>


                        {{-- ========================================= --}}
                        {{-- CART ITEMS --}}
                        {{-- ========================================= --}}

                        <div x-show="cart.length > 0" class="max-h-[480px]
                                       divide-y divide-slate-100
                                       overflow-y-auto">

                            <template x-for="(item, index) in cart" :key="item.id">

                                <div class="p-5">

                                    {{-- PRODUCT HEADER --}}

                                    <div class="flex items-start
                                                   justify-between gap-3">

                                        <div class="min-w-0">

                                            <h3 class="font-bold text-slate-800" x-text="item.nama"></h3>

                                            <p class="mt-1 text-sm
                                                           text-slate-500">

                                                <span x-text="item.warna"></span>

                                                /

                                                <span x-text="item.ukuran"></span>

                                            </p>

                                            <p class="mt-1 text-xs
                                                           text-slate-400">
                                                SKU:
                                                <span x-text="item.sku"></span>
                                            </p>

                                        </div>


                                        <button type="button" @click="removeItem(index)" class="rounded-lg px-2 py-1
                                                       text-red-400 transition
                                                       hover:bg-red-50
                                                       hover:text-red-600">
                                            ✕
                                        </button>

                                    </div>


                                    {{-- INPUTS --}}

                                    <div class="mt-4 grid
                                                   grid-cols-2 gap-3">

                                        {{-- QTY --}}

                                        <div>

                                            <label class="mb-1 block text-xs
                                                           font-semibold
                                                           text-slate-500">
                                                Qty Rencana
                                            </label>

                                            <div class="flex">

                                                <button type="button" @click="
                                                            decreaseQty(index)
                                                        " class="rounded-l-lg
                                                               border
                                                               border-slate-300
                                                               bg-slate-50
                                                               px-3
                                                               hover:bg-slate-100">
                                                    −
                                                </button>

                                                <input type="number" min="1" x-model.number="item.qty" class="w-full border-y
                                                               border-slate-300
                                                               px-2 py-2
                                                               text-center
                                                               outline-none">

                                                <button type="button" @click="
                                                            increaseQty(index)
                                                        " class="rounded-r-lg
                                                               border
                                                               border-slate-300
                                                               bg-slate-50
                                                               px-3
                                                               hover:bg-slate-100">
                                                    +
                                                </button>

                                            </div>

                                        </div>


                                        {{-- ESTIMASI HARGA --}}

                                        <div>

                                            <label class="mb-1 block text-xs
                                                           font-semibold
                                                           text-slate-500">
                                                Estimasi / Unit
                                            </label>

                                            <input type="number" min="0" step="1" x-model.number="
                                                        item.estimasi_harga
                                                    " placeholder="0" class="w-full rounded-lg
                                                           border
                                                           border-slate-300
                                                           px-3 py-2
                                                           outline-none
                                                           focus:border-blue-500">

                                        </div>

                                    </div>


                                    {{-- SUBTOTAL --}}

                                    <div class="mt-4 flex items-center
                                                   justify-between
                                                   rounded-lg bg-slate-50
                                                   px-3 py-2">

                                        <span class="text-xs
                                                       text-slate-500">
                                            Estimasi Subtotal
                                        </span>

                                        <span class="text-sm font-bold
                                                       text-slate-700" x-text="
                                                    rupiah(
                                                        item.qty
                                                        *
                                                        item.estimasi_harga
                                                    )
                                                "></span>

                                    </div>

                                </div>

                            </template>

                        </div>


                        {{-- ========================================= --}}
                        {{-- SUMMARY --}}
                        {{-- ========================================= --}}

                        <div class="border-t border-slate-100 p-6">

                            <div class="space-y-3">

                                <div class="flex items-center
                                               justify-between text-sm">

                                    <span class="text-slate-500">
                                        Total Varian
                                    </span>

                                    <span class="font-semibold
                                                   text-slate-700" x-text="cart.length"></span>

                                </div>


                                <div class="flex items-center
                                               justify-between text-sm">

                                    <span class="text-slate-500">
                                        Total Unit
                                    </span>

                                    <span class="font-semibold
                                                   text-slate-700" x-text="totalQty"></span>

                                </div>


                                <div class="border-t border-dashed
                                               border-slate-200 pt-4">

                                    <div class="flex items-center
                                                   justify-between">

                                        <span class="font-bold
                                                       text-slate-800">
                                            Estimasi Total
                                        </span>

                                        <span class="text-xl font-bold
                                                       text-blue-600" x-text="
                                                    rupiah(totalEstimasi)
                                                "></span>

                                    </div>

                                </div>

                            </div>


                            {{-- SUBMIT --}}

                            <button type="submit" :disabled="
                                        cart.length === 0
                                        ||
                                        !supplierId
                                    " :class="
                                        cart.length === 0
                                        ||
                                        !supplierId

                                            ? 'bg-slate-300 cursor-not-allowed'

                                            : 'bg-blue-600 hover:bg-blue-700'
                                    " class="mt-6 w-full rounded-xl
                                           px-5 py-4 text-sm
                                           font-bold text-white
                                           transition">
                                Simpan Perencanaan
                            </button>


                            <p class="mt-3 text-center
                                           text-xs leading-relaxed
                                           text-slate-400">
                                Menyimpan perencanaan tidak akan
                                menambah stok barang.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- HIDDEN CART INPUT --}}
            {{-- Data ini yang dibaca controller Laravel --}}
            {{-- ===================================================== --}}

            <template x-for="(item, index) in cart" :key="'input-' + item.id">

                <div>

                    <input type="hidden" :name="`cart[${index}][varian_id]`" :value="item.id">

                    <input type="hidden" :name="`cart[${index}][qty]`" :value="item.qty">

                    <input type="hidden" :name="`cart[${index}][estimasi_harga]`" :value="item.estimasi_harga">

                </div>

            </template>

        </form>

    </div>


    {{-- ============================================================= --}}
    {{-- ALPINE JS --}}
    {{-- ============================================================= --}}

    <script>

        function purchasePlan() {

            return {

                search: '',

                selectedCategory: 'Semua',

                supplierId:
                    @js(old('supplier_id', '')),

                supplierName: '',

                cart: [],


                /*
                |--------------------------------------------------------------------------
                | INITIALIZE
                |--------------------------------------------------------------------------
                */

                init() {

                    this.$nextTick(() => {

                        this.updateSupplierName();

                    });

                },


                /*
                |--------------------------------------------------------------------------
                | SUPPLIER
                |--------------------------------------------------------------------------
                */

                updateSupplierName() {

                    const select =
                        document.getElementById(
                            'supplier_id'
                        );

                    if (
                        !select
                        ||
                        !select.value
                    ) {

                        this.supplierName = '';

                        return;

                    }

                    this.supplierName =
                        select.options[
                            select.selectedIndex
                        ].text;

                },


                /*
                |--------------------------------------------------------------------------
                | FILTER PRODUCT
                |--------------------------------------------------------------------------
                */

                productVisible(
                    nama,
                    sku,
                    warna,
                    ukuran,
                    kategori
                ) {

                    const keyword =
                        this.search
                            .toLowerCase()
                            .trim();

                    const text =
                        (
                            nama
                            + ' '
                            + sku
                            + ' '
                            + warna
                            + ' '
                            + ukuran
                        )
                            .toLowerCase();


                    const matchSearch =
                        keyword === ''
                        ||
                        text.includes(keyword);


                    const matchCategory =
                        this.selectedCategory ===
                        'Semua'
                        ||
                        kategori ===
                        this.selectedCategory;


                    return (
                        matchSearch
                        &&
                        matchCategory
                    );

                },


                /*
                |--------------------------------------------------------------------------
                | ADD ITEM
                |--------------------------------------------------------------------------
                */

                addItem(
                    id,
                    nama,
                    warna,
                    ukuran,
                    sku,
                    stok
                ) {

                    const existing = this.cart.find(
                        item => Number(item.id) === Number(id)
                    );

                    if (existing) {

                        existing.qty =
                            Number(existing.qty) + 1;

                        return;
                    }

                    this.cart.push({

                        id: Number(id),

                        nama: nama,

                        warna: warna,

                        ukuran: ukuran,

                        sku: sku,

                        stok: Number(stok),

                        qty: 1,

                        estimasi_harga: 0

                    });

                },

                /*
                |--------------------------------------------------------------------------
                | REMOVE ITEM
                |--------------------------------------------------------------------------
                */

                removeItem(index) {

                    this.cart.splice(
                        index,
                        1
                    );

                },


                /*
                |--------------------------------------------------------------------------
                | CLEAR CART
                |--------------------------------------------------------------------------
                */

                clearCart() {

                    if (
                        confirm(
                            'Kosongkan semua barang dari perencanaan?'
                        )
                    ) {

                        this.cart = [];

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | QUANTITY
                |--------------------------------------------------------------------------
                */

                increaseQty(index) {

                    this.cart[index].qty++;

                },


                decreaseQty(index) {

                    if (
                        this.cart[index].qty > 1
                    ) {

                        this.cart[index].qty--;

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                get totalQty() {

                    return this.cart.reduce(

                        (
                            total,
                            item
                        ) => {

                            return (
                                total
                                +
                                Number(
                                    item.qty
                                    ||
                                    0
                                )
                            );

                        },

                        0

                    );

                },


                get totalEstimasi() {

                    return this.cart.reduce(

                        (
                            total,
                            item
                        ) => {

                            return (
                                total
                                +
                                (
                                    Number(
                                        item.qty
                                        ||
                                        0
                                    )
                                    *
                                    Number(
                                        item.estimasi_harga
                                        ||
                                        0
                                    )
                                )
                            );

                        },

                        0

                    );

                },


                /*
                |--------------------------------------------------------------------------
                | FORMAT RUPIAH
                |--------------------------------------------------------------------------
                */

                rupiah(value) {

                    return new Intl
                        .NumberFormat(
                            'id-ID',
                            {
                                style:
                                    'currency',

                                currency:
                                    'IDR',

                                maximumFractionDigits:
                                    0,
                            }
                        )
                        .format(
                            Number(
                                value
                                ||
                                0
                            )
                        );

                },


                /*
                |--------------------------------------------------------------------------
                | VALIDASI SEBELUM SUBMIT
                |--------------------------------------------------------------------------
                */

                prepareSubmit(event) {

                    if (
                        !this.supplierId
                    ) {

                        event.preventDefault();

                        alert(
                            'Pilih supplier terlebih dahulu.'
                        );

                        return;

                    }


                    if (
                        this.cart.length === 0
                    ) {

                        event.preventDefault();

                        alert(
                            'Tambahkan minimal satu barang ke perencanaan.'
                        );

                        return;

                    }


                    const invalidQty =
                        this.cart.some(
                            item =>
                                !item.qty
                                ||
                                item.qty < 1
                        );


                    if (invalidQty) {

                        event.preventDefault();

                        alert(
                            'Qty rencana minimal 1.'
                        );

                    }

                },

            };

        }

    </script>

@endsection