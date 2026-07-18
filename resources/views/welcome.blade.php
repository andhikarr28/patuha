<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Patuha Outdoor | Sistem Informasi Penjualan & Pembelian
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


<body class="min-h-screen bg-slate-50 text-slate-900">

<div class="min-h-screen flex flex-col">


    {{-- NAVBAR --}}
    <header class="w-full bg-white border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-6 lg:px-8
                    h-20 flex items-center justify-between">

            {{-- BRAND --}}
            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-xl
                            bg-slate-900
                            flex items-center justify-center
                            text-white font-bold text-xl">

                    P

                </div>

                <div>

                    <h1 class="font-bold text-lg leading-tight">
                        Patuha Outdoor
                    </h1>

                    <p class="text-xs text-slate-500">
                        Management System
                    </p>

                </div>

            </div>


            {{-- AUTH --}}
            <div>

                @auth

                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2
                              bg-slate-900 hover:bg-slate-800
                              text-white
                              px-5 py-2.5
                              rounded-xl
                              font-semibold
                              text-sm
                              transition">

                        Dashboard

                        <span>
                            →
                        </span>

                    </a>

                @else

                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2
                              bg-slate-900 hover:bg-slate-800
                              text-white
                              px-5 py-2.5
                              rounded-xl
                              font-semibold
                              text-sm
                              transition">

                        Masuk Sistem

                        <span>
                            →
                        </span>

                    </a>

                @endauth

            </div>

        </div>

    </header>



    {{-- HERO --}}
    <main class="flex-1">

        <section class="max-w-7xl mx-auto
                        px-6 lg:px-8
                        py-16 lg:py-24">

            <div class="grid
                        lg:grid-cols-2
                        gap-16
                        items-center">


                {{-- LEFT CONTENT --}}
                <div>

                    {{-- BADGE --}}
                    <div class="inline-flex items-center gap-2
                                bg-blue-50
                                border border-blue-100
                                text-blue-700
                                px-4 py-2
                                rounded-full
                                text-sm font-semibold
                                mb-6">

                        <span class="w-2 h-2
                                     rounded-full
                                     bg-blue-600">

                        </span>

                        Sistem Informasi Terintegrasi

                    </div>


                    {{-- TITLE --}}
                    <h1 class="text-4xl
                               md:text-5xl
                               lg:text-6xl
                               font-bold
                               tracking-tight
                               leading-tight
                               text-slate-900">

                        Kelola Operasional

                        <span class="text-blue-600">
                            Patuha Outdoor
                        </span>

                        dalam satu sistem.

                    </h1>


                    {{-- DESCRIPTION --}}
                    <p class="mt-6
                              text-lg
                              leading-relaxed
                              text-slate-600
                              max-w-xl">

                        Sistem informasi untuk membantu pengelolaan
                        penjualan, pembelian, stok barang, supplier,
                        serta integrasi marketplace secara lebih
                        terstruktur dan efisien.

                    </p>


                    {{-- CTA --}}
                    <div class="flex flex-wrap gap-3 mt-8">

                        @auth

                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center gap-3
                                      bg-blue-600 hover:bg-blue-700
                                      text-white
                                      px-6 py-3.5
                                      rounded-xl
                                      font-semibold
                                      transition
                                      shadow-sm">

                                Buka Dashboard

                                <span>
                                    →
                                </span>

                            </a>

                        @else

                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-3
                                      bg-blue-600 hover:bg-blue-700
                                      text-white
                                      px-6 py-3.5
                                      rounded-xl
                                      font-semibold
                                      transition
                                      shadow-sm">

                                Masuk ke Sistem

                                <span>
                                    →
                                </span>

                            </a>

                        @endauth


                        <a href="#fitur"
                           class="inline-flex items-center
                                  border border-slate-300
                                  hover:bg-white
                                  text-slate-700
                                  px-6 py-3.5
                                  rounded-xl
                                  font-semibold
                                  transition">

                            Lihat Fitur

                        </a>

                    </div>


                    {{-- SMALL INFO --}}
                    <div class="flex flex-wrap gap-6 mt-10
                                text-sm text-slate-500">

                        <div class="flex items-center gap-2">

                            <span class="text-green-600">
                                ✓
                            </span>

                            Stok otomatis

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="text-green-600">
                                ✓
                            </span>

                            Transaksi terintegrasi

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="text-green-600">
                                ✓
                            </span>

                            Marketplace

                        </div>

                    </div>

                </div>



                {{-- RIGHT SIDE --}}
                <div class="relative">

                    {{-- BACKGROUND DECORATION --}}
                    <div class="absolute
                                -top-10 -right-10
                                w-72 h-72
                                bg-blue-100
                                rounded-full
                                blur-3xl
                                opacity-60">

                    </div>


                    <div class="relative
                                bg-white
                                border border-slate-200
                                rounded-3xl
                                shadow-xl
                                p-6 lg:p-8">


                        {{-- MOCK APP HEADER --}}
                        <div class="flex items-center justify-between
                                    pb-5
                                    border-b border-slate-100">

                            <div>

                                <p class="text-sm text-slate-500">
                                    Sistem Informasi
                                </p>

                                <h2 class="text-xl font-bold mt-1">
                                    Patuha Outdoor
                                </h2>

                            </div>

                            <div class="flex items-center gap-2">

                                <span class="w-3 h-3
                                             bg-red-400
                                             rounded-full">

                                </span>

                                <span class="w-3 h-3
                                             bg-yellow-400
                                             rounded-full">

                                </span>

                                <span class="w-3 h-3
                                             bg-green-400
                                             rounded-full">

                                </span>

                            </div>

                        </div>



                        {{-- MINI DASHBOARD --}}
                        <div class="grid grid-cols-2 gap-4 mt-6">

                            {{-- PENJUALAN --}}
                            <div class="bg-green-50
                                        border border-green-100
                                        rounded-2xl
                                        p-5">

                                <div class="w-10 h-10
                                            bg-green-100
                                            rounded-xl
                                            flex items-center
                                            justify-center
                                            text-xl">

                                    ↗

                                </div>

                                <p class="text-sm
                                          text-slate-500
                                          mt-4">

                                    Penjualan

                                </p>

                                <p class="font-bold
                                          text-slate-900
                                          mt-1">

                                    Transaksi POS

                                </p>

                            </div>


                            {{-- PEMBELIAN --}}
                            <div class="bg-blue-50
                                        border border-blue-100
                                        rounded-2xl
                                        p-5">

                                <div class="w-10 h-10
                                            bg-blue-100
                                            rounded-xl
                                            flex items-center
                                            justify-center
                                            text-xl">

                                    ↓

                                </div>

                                <p class="text-sm
                                          text-slate-500
                                          mt-4">

                                    Pembelian

                                </p>

                                <p class="font-bold
                                          text-slate-900
                                          mt-1">

                                    Penerimaan Barang

                                </p>

                            </div>


                            {{-- INVENTORY --}}
                            <div class="bg-orange-50
                                        border border-orange-100
                                        rounded-2xl
                                        p-5">

                                <div class="w-10 h-10
                                            bg-orange-100
                                            rounded-xl
                                            flex items-center
                                            justify-center
                                            text-xl">

                                    ◫

                                </div>

                                <p class="text-sm
                                          text-slate-500
                                          mt-4">

                                    Barang

                                </p>

                                <p class="font-bold
                                          text-slate-900
                                          mt-1">

                                    Stok Terintegrasi

                                </p>

                            </div>


                            {{-- MARKETPLACE --}}
                            <div class="bg-purple-50
                                        border border-purple-100
                                        rounded-2xl
                                        p-5">

                                <div class="w-10 h-10
                                            bg-purple-100
                                            rounded-xl
                                            flex items-center
                                            justify-center
                                            text-xl">

                                    ◉

                                </div>

                                <p class="text-sm
                                          text-slate-500
                                          mt-4">

                                    Marketplace

                                </p>

                                <p class="font-bold
                                          text-slate-900
                                          mt-1">

                                    Sinkronisasi

                                </p>

                            </div>

                        </div>



                        {{-- FLOW --}}
                        <div class="mt-5
                                    bg-slate-50
                                    border border-slate-100
                                    rounded-2xl
                                    p-5">

                            <p class="text-xs
                                      uppercase
                                      tracking-wider
                                      font-semibold
                                      text-slate-400">

                                Alur Pembelian

                            </p>

                            <div class="flex
                                        items-center
                                        justify-between
                                        gap-2
                                        mt-4
                                        text-xs
                                        font-semibold">

                                <div class="bg-white
                                            border
                                            rounded-lg
                                            px-3 py-2">

                                    Perencanaan

                                </div>

                                <span class="text-slate-400">
                                    →
                                </span>

                                <div class="bg-white
                                            border
                                            rounded-lg
                                            px-3 py-2">

                                    Penerimaan

                                </div>

                                <span class="text-slate-400">
                                    →
                                </span>

                                <div class="bg-blue-600
                                            text-white
                                            rounded-lg
                                            px-3 py-2">

                                    Stok Update

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>



        {{-- FEATURES --}}
        <section id="fitur"
                 class="bg-white
                        border-y border-slate-200">

            <div class="max-w-7xl mx-auto
                        px-6 lg:px-8
                        py-16">


                <div class="text-center max-w-2xl mx-auto">

                    <p class="text-blue-600
                              font-semibold
                              text-sm">

                        FITUR SISTEM

                    </p>

                    <h2 class="text-3xl
                               font-bold
                               mt-2">

                        Mendukung proses operasional toko

                    </h2>

                    <p class="text-slate-500 mt-3">

                        Seluruh proses utama saling terhubung
                        untuk menjaga konsistensi transaksi dan stok.

                    </p>

                </div>



                <div class="grid
                            md:grid-cols-2
                            lg:grid-cols-4
                            gap-5
                            mt-10">


                    {{-- FEATURE 1 --}}
                    <div class="border border-slate-200
                                rounded-2xl p-6
                                hover:shadow-lg
                                hover:-translate-y-1
                                transition">

                        <div class="w-12 h-12
                                    bg-green-50
                                    text-green-600
                                    rounded-xl
                                    flex items-center
                                    justify-center
                                    text-2xl">

                            ↗

                        </div>

                        <h3 class="font-bold text-lg mt-5">
                            Penjualan
                        </h3>

                        <p class="text-sm
                                  text-slate-500
                                  leading-relaxed
                                  mt-2">

                            Pencatatan transaksi penjualan
                            dengan pengurangan stok otomatis.

                        </p>

                    </div>


                    {{-- FEATURE 2 --}}
                    <div class="border border-slate-200
                                rounded-2xl p-6
                                hover:shadow-lg
                                hover:-translate-y-1
                                transition">

                        <div class="w-12 h-12
                                    bg-blue-50
                                    text-blue-600
                                    rounded-xl
                                    flex items-center
                                    justify-center
                                    text-2xl">

                            ↓

                        </div>

                        <h3 class="font-bold text-lg mt-5">
                            Pembelian
                        </h3>

                        <p class="text-sm
                                  text-slate-500
                                  leading-relaxed
                                  mt-2">

                            Perencanaan dan penerimaan barang
                            dengan pembaruan stok terintegrasi.

                        </p>

                    </div>


                    {{-- FEATURE 3 --}}
                    <div class="border border-slate-200
                                rounded-2xl p-6
                                hover:shadow-lg
                                hover:-translate-y-1
                                transition">

                        <div class="w-12 h-12
                                    bg-orange-50
                                    text-orange-600
                                    rounded-xl
                                    flex items-center
                                    justify-center
                                    text-2xl">

                            ◫

                        </div>

                        <h3 class="font-bold text-lg mt-5">
                            Manajemen Barang
                        </h3>

                        <p class="text-sm
                                  text-slate-500
                                  leading-relaxed
                                  mt-2">

                            Kelola produk, varian, kategori,
                            stok dan batas minimum persediaan.

                        </p>

                    </div>


                    {{-- FEATURE 4 --}}
                    <div class="border border-slate-200
                                rounded-2xl p-6
                                hover:shadow-lg
                                hover:-translate-y-1
                                transition">

                        <div class="w-12 h-12
                                    bg-purple-50
                                    text-purple-600
                                    rounded-xl
                                    flex items-center
                                    justify-center
                                    text-2xl">

                            ◉

                        </div>

                        <h3 class="font-bold text-lg mt-5">
                            Marketplace
                        </h3>

                        <p class="text-sm
                                  text-slate-500
                                  leading-relaxed
                                  mt-2">

                            Integrasi produk, order dan stok
                            marketplace dengan sistem toko.

                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>



    {{-- FOOTER --}}
    <footer class="bg-slate-950 text-slate-400">

        <div class="max-w-7xl mx-auto
                    px-6 lg:px-8
                    py-6
                    flex flex-col md:flex-row
                    gap-3
                    justify-between
                    items-center
                    text-sm">

            <p>
                © {{ date('Y') }} Patuha Outdoor
            </p>

            <p>
                Sistem Informasi Penjualan & Pembelian
            </p>

        </div>

    </footer>


</div>

</body>

</html>