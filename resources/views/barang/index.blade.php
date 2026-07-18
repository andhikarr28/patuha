@extends('layouts.app')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="text-3xl font-bold text-slate-900">
                    Master Barang
                </h1>

                <p class="mt-1 text-slate-500">
                    Kelola data barang dan seluruh varian produk.
                </p>
            </div>

            <a href="{{ route('barang.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl
                       bg-blue-600 px-5 py-3 font-semibold text-white
                       transition hover:bg-blue-700">
                <span>+</span>
                Tambah Barang
            </a>

        </div>


        {{-- FLASH MESSAGE --}}
        @if(session('success'))

            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">

                {{ session('error') }}

            </div>

        @endif


        {{-- SEARCH & FILTER --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-col gap-4 lg:flex-row">

                {{-- SEARCH --}}
                <div class="relative flex-1">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">

                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                        </svg>

                    </div>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama barang, artikel, atau brand..." class="w-full rounded-xl border border-slate-300
                               py-3 pl-12 pr-4
                               outline-none transition
                               focus:border-blue-500
                               focus:ring-2 focus:ring-blue-100">

                </div>


                {{-- FILTER KATEGORI --}}
                <select name="kategori_id" class="rounded-xl border border-slate-300
                           px-4 py-3
                           outline-none transition
                           focus:border-blue-500
                           focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Kategori
                    </option>

                    @foreach($kategori as $item)

                        <option value="{{ $item->id }}" {{ request('kategori_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_kategori }}
                        </option>

                    @endforeach

                </select>


                {{-- BUTTON FILTER --}}
                <button type="submit" class="rounded-xl bg-slate-900
                           px-6 py-3 font-semibold text-white
                           transition hover:bg-slate-800">
                    Cari
                </button>


                {{-- RESET --}}
                @if(request('search') || request('kategori_id'))

                    <a href="{{ route('barang.index') }}" class="flex items-center justify-center
                                   rounded-xl border border-slate-300
                                   px-5 py-3 font-semibold text-slate-600
                                   transition hover:bg-slate-50">
                        Reset
                    </a>

                @endif

            </form>

        </div>


        {{-- DAFTAR BARANG --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- TABLE HEADER --}}
            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-xl font-bold text-slate-900">
                            Daftar Barang
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $barang->count() }} barang ditemukan
                        </p>

                    </div>

                </div>

            </div>


            @if($barang->count())

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">

                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">

                                <th class="px-6 py-4">
                                    Barang
                                </th>

                                <th class="px-6 py-4">
                                    Kategori
                                </th>

                                <th class="px-6 py-4">
                                    Brand
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Varian
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Total Stok
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach($barang as $item)

                                @php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Data hasil agregasi dari controller
                                    |--------------------------------------------------------------------------
                                    |
                                    | withCount('varians')
                                    | menghasilkan:
                                    |
                                    | $item->varians_count
                                    |
                                    | withSum('varians', 'stok')
                                    | menghasilkan:
                                    |
                                    | $item->varians_sum_stok
                                    |
                                    */

                                    $jumlahVarian =
                                        $item->varians_count ?? 0;

                                    $totalStok =
                                        $item->varians_sum_stok ?? 0;

                                @endphp


                                <tr class="transition hover:bg-slate-50/70">

                                    {{-- BARANG --}}
                                    <td class="px-6 py-5">

                                        <div class="flex min-w-[280px] items-center gap-4">

                                            {{-- FOTO --}}
                                            <div class="flex h-16 w-16 shrink-0 items-center justify-center
                                                                overflow-hidden rounded-xl bg-slate-100">

                                                @if($item->foto)

                                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}"
                                                        class="h-full w-full object-cover">

                                                @else

                                                    <div class="text-center">

                                                        <div class="text-xl">
                                                            📦
                                                        </div>

                                                        <span class="text-[10px] text-slate-400">
                                                            No Image
                                                        </span>

                                                    </div>

                                                @endif

                                            </div>


                                            {{-- INFO --}}
                                            <div>

                                                <a href="{{ route('barang.show', $item->id) }}" class="font-bold text-slate-900
                                                                   transition hover:text-blue-600">
                                                    {{ $item->nama_barang }}
                                                </a>


                                                @if($item->artikel)

                                                    <p class="mt-1 text-sm text-slate-500">

                                                        Artikel:

                                                        <span class="font-medium">
                                                            {{ $item->artikel }}
                                                        </span>

                                                    </p>

                                                @endif


                                                @if($item->kode_seri)

                                                    <p class="mt-1 text-xs text-slate-400">

                                                        {{ $item->kode_seri }}

                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- KATEGORI --}}
                                    <td class="px-6 py-5">

                                        <span class="inline-flex rounded-full
                                                             bg-blue-50 px-3 py-1
                                                             text-sm font-medium text-blue-700">

                                            {{ $item->kategori?->nama_kategori ?? '-' }}

                                        </span>

                                    </td>


                                    {{-- BRAND --}}
                                    <td class="px-6 py-5 text-slate-600">

                                        {{ $item->brand ?: '-' }}

                                    </td>


                                    {{-- JUMLAH VARIAN --}}
                                    <td class="px-6 py-5 text-center">

                                        <div class="inline-flex items-center gap-2 rounded-lg
                                                            bg-slate-100 px-3 py-2">

                                            <span class="font-bold text-slate-900">

                                                {{ $jumlahVarian }}

                                            </span>

                                            <span class="text-sm text-slate-500">

                                                Varian

                                            </span>

                                        </div>

                                    </td>


                                    {{-- TOTAL STOK --}}
                                    <td class="px-6 py-5 text-center">

                                        @if($jumlahVarian == 0)

                                            <span class="rounded-lg bg-slate-100
                                                                     px-3 py-2 text-sm text-slate-500">

                                                Belum ada varian

                                            </span>

                                        @elseif($totalStok <= 0)

                                            <span class="rounded-lg bg-red-50
                                                                     px-3 py-2 font-semibold text-red-600">

                                                Habis

                                            </span>

                                        @else

                                            <span class="rounded-lg bg-green-50
                                                                     px-3 py-2 font-semibold text-green-700">

                                                {{ number_format($totalStok, 0, ',', '.') }} unit

                                            </span>

                                        @endif

                                    </td>


                                    {{-- AKSI --}}
                                    <td class="px-6 py-5">

                                        <div class="flex justify-end gap-2">

                                            {{-- DETAIL --}}
                                            <a href="{{ route('barang.show', $item->id) }}" class="rounded-lg bg-blue-600
                                                               px-4 py-2 text-sm font-semibold text-white
                                                               transition hover:bg-blue-700">
                                                Detail
                                            </a>


                                            {{-- EDIT --}}
                                            <a href="{{ route('barang.edit', $item->id) }}" class="rounded-lg border border-slate-300
                                                               px-4 py-2 text-sm font-semibold text-slate-700
                                                               transition hover:bg-slate-100">
                                                Edit
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="flex min-h-[350px] flex-col items-center justify-center px-6 py-16">

                    <div class="mb-4 flex h-20 w-20 items-center justify-center
                                    rounded-full bg-slate-100 text-4xl">

                        📦

                    </div>

                    <h3 class="text-xl font-bold text-slate-900">

                        Barang Tidak Ditemukan

                    </h3>

                    <p class="mt-2 max-w-md text-center text-slate-500">

                        @if(request('search') || request('kategori_id'))

                            Tidak ada barang yang sesuai dengan pencarian atau filter yang dipilih.

                        @else

                            Belum ada data barang. Tambahkan barang pertama untuk mulai mengelola produk.

                        @endif

                    </p>


                    @if(request('search') || request('kategori_id'))

                        <a href="{{ route('barang.index') }}" class="mt-5 rounded-xl bg-slate-900
                                           px-5 py-3 font-semibold text-white">
                            Reset Pencarian
                        </a>

                    @else

                        <a href="{{ route('barang.create') }}" class="mt-5 rounded-xl bg-blue-600
                                           px-5 py-3 font-semibold text-white">
                            + Tambah Barang
                        </a>

                    @endif

                </div>

            @endif

        </div>

    </div>

@endsection