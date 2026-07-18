@extends('layouts.app')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <a href="{{ route('barang.index') }}" class="mb-3 inline-flex items-center gap-2
                           text-sm font-medium text-slate-500
                           transition hover:text-blue-600">
                    ← Kembali ke Master Barang
                </a>

                <h1 class="text-3xl font-bold text-slate-900">

                    {{ $barang->nama_barang }}

                </h1>

                <p class="mt-1 text-slate-500">

                    Informasi barang dan seluruh varian produk.

                </p>

            </div>


            <div class="flex flex-wrap gap-3">

                {{-- EDIT BARANG --}}
                <a href="{{ route('barang.edit', $barang->id) }}" class="inline-flex items-center justify-center
                           rounded-xl border border-slate-300
                           bg-white px-5 py-3
                           font-semibold text-slate-700
                           transition hover:bg-slate-50">
                    ✏️ Edit Barang
                </a>


                {{-- TAMBAH VARIAN --}}
                <a href="{{ route('varian.create', [
        'barang_id' => $barang->id
    ]) }}" class="inline-flex items-center justify-center
                           rounded-xl bg-blue-600
                           px-5 py-3 font-semibold text-white
                           transition hover:bg-blue-700">
                    + Tambah Varian
                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ========================================================= --}}

        @if(session('success'))

            <div class="rounded-xl border border-green-200
                            bg-green-50 px-5 py-4 text-green-700">

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="rounded-xl border border-red-200
                            bg-red-50 px-5 py-4 text-red-700">

                {{ session('error') }}

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- INFORMASI BARANG --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- PRODUCT INFO --}}
            <div class="xl:col-span-2">

                <div class="h-full rounded-2xl
                            border border-slate-200
                            bg-white p-6 shadow-sm">

                    <div class="flex flex-col gap-6 md:flex-row">

                        {{-- FOTO --}}
                        <div class="shrink-0">

                            <div class="flex h-44 w-full
                                        items-center justify-center
                                        overflow-hidden rounded-2xl
                                        bg-slate-100 md:w-44">

                                @if($barang->foto)

                                                            <img src="{{ asset(
                                        'storage/' . $barang->foto
                                    ) }}" alt="{{ $barang->nama_barang }}" class="h-full w-full object-cover">

                                @else

                                    <div class="text-center">

                                        <div class="text-5xl">
                                            📦
                                        </div>

                                        <p class="mt-2 text-sm text-slate-400">
                                            No Image
                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>


                        {{-- INFO --}}
                        <div class="flex-1">

                            <div class="mb-5">

                                <span class="inline-flex rounded-full
                                             bg-blue-50 px-3 py-1
                                             text-sm font-semibold
                                             text-blue-700">

                                    {{
        $barang->kategori
                ?->nama_kategori
        ?? 'Tanpa Kategori'
                                    }}

                                </span>

                            </div>


                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                {{-- NAMA --}}
                                <div>

                                    <p class="text-sm text-slate-500">
                                        Nama Barang
                                    </p>

                                    <p class="mt-1 font-bold text-slate-900">

                                        {{ $barang->nama_barang }}

                                    </p>

                                </div>


                                {{-- BRAND --}}
                                <div>

                                    <p class="text-sm text-slate-500">
                                        Brand
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">

                                        {{ $barang->brand ?: '-' }}

                                    </p>

                                </div>


                                {{-- ARTIKEL --}}
                                <div>

                                    <p class="text-sm text-slate-500">
                                        Artikel
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">

                                        {{ $barang->artikel ?: '-' }}

                                    </p>

                                </div>


                                {{-- KODE SERI --}}
                                <div>

                                    <p class="text-sm text-slate-500">
                                        Kode Seri
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">

                                        {{ $barang->kode_seri ?: '-' }}

                                    </p>

                                </div>

                            </div>


                            {{-- SPESIFIKASI --}}
                            @if($barang->spesifikasi)

                                <div class="mt-6 border-t
                                                border-slate-100 pt-5">

                                    <p class="text-sm text-slate-500">
                                        Spesifikasi
                                    </p>

                                    <p class="mt-2 leading-relaxed
                                                  text-slate-700">

                                        {{ $barang->spesifikasi }}

                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- STATISTIK --}}
            {{-- ===================================================== --}}

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 xl:grid-cols-1">

                {{-- JUMLAH VARIAN --}}
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500">
                                Total Varian
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900">

                                {{ $jumlahVarian }}

                            </p>

                        </div>

                        <div class="flex h-12 w-12
                                    items-center justify-center
                                    rounded-xl bg-blue-50 text-2xl">

                            🎨

                        </div>

                    </div>

                </div>


                {{-- TOTAL STOK --}}
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500">
                                Total Stok
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900">

                                {{ number_format(
        $totalStok,
        0,
        ',',
        '.'
    ) }}

                            </p>

                            <p class="text-sm text-slate-400">
                                unit
                            </p>

                        </div>

                        <div class="flex h-12 w-12
                                    items-center justify-center
                                    rounded-xl bg-green-50 text-2xl">

                            📦

                        </div>

                    </div>

                </div>


                {{-- STOK MENIPIS --}}
                <div class="rounded-2xl border
                            {{ $stokMenipis > 0
        ? 'border-red-200 bg-red-50'
        : 'border-slate-200 bg-white'
                            }}
                            p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500">
                                Stok Menipis
                            </p>

                            <p class="mt-2 text-3xl font-bold
                                      {{ $stokMenipis > 0
        ? 'text-red-600'
        : 'text-slate-900'
                                      }}">

                                {{ $stokMenipis }}

                            </p>

                            <p class="text-sm text-slate-400">
                                varian
                            </p>

                        </div>

                        <div class="flex h-12 w-12
                                    items-center justify-center
                                    rounded-xl
                                    {{ $stokMenipis > 0
        ? 'bg-red-100'
        : 'bg-slate-100'
                                    }}
                                    text-2xl">

                            ⚠️

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DAFTAR VARIAN --}}
        {{-- ========================================================= --}}

        <div class="overflow-hidden rounded-2xl
                    border border-slate-200
                    bg-white shadow-sm">

            {{-- HEADER --}}
            <div class="flex flex-col gap-4
                        border-b border-slate-200
                        px-6 py-5
                        sm:flex-row
                        sm:items-center
                        sm:justify-between">

                <div>

                    <h2 class="text-xl font-bold text-slate-900">

                        Varian Produk

                    </h2>

                    <p class="mt-1 text-sm text-slate-500">

                        Kelola warna, ukuran, harga, dan stok
                        untuk {{ $barang->nama_barang }}.

                    </p>

                </div>


                <div class="rounded-lg bg-slate-100
                            px-4 py-2 text-sm font-semibold
                            text-slate-600">

                    {{ $jumlahVarian }} Varian

                </div>

            </div>


            @if($barang->varians->count())

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">

                            <tr class="text-left text-xs
                                           font-semibold uppercase
                                           tracking-wide text-slate-500">

                                <th class="px-6 py-4">
                                    Varian
                                </th>

                                <th class="px-6 py-4">
                                    SKU
                                </th>

                                <th class="px-6 py-4">
                                    Harga Beli
                                </th>

                                <th class="px-6 py-4">
                                    Harga Jual
                                </th>

                                <th class="px-6 py-4">
                                    Margin
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Stok
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Min. Stok
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach($barang->varians as $varian)

                                            @php

                                                $hargaBeli =
                                                    $varian->harga_beli ?? 0;

                                                $hargaJual =
                                                    $varian->harga_jual ?? 0;

                                                $margin =
                                                    $hargaJual - $hargaBeli;

                                                $stokMenipisVarian =
                                                    $varian->stok
                                                    <= $varian->stok_minimum;

                                            @endphp


                                            <tr class="transition
                                                               hover:bg-slate-50/70">

                                                {{-- VARIAN --}}
                                                <td class="px-6 py-5">

                                                    <div>

                                                        <p class="font-bold
                                                                          text-slate-900">

                                                            {{ $varian->warna ?: '-' }}

                                                            <span class="font-normal
                                                                                 text-slate-400">
                                                                /
                                                            </span>

                                                            {{ $varian->ukuran ?: '-' }}

                                                        </p>

                                                        <p class="mt-1 text-xs
                                                                          text-slate-400">

                                                            ID Varian:
                                                            {{ $varian->id }}

                                                        </p>

                                                    </div>

                                                </td>


                                                {{-- SKU --}}
                                                <td class="px-6 py-5">

                                                    <span class="rounded-lg
                                                                         bg-slate-100
                                                                         px-3 py-1.5
                                                                         font-mono text-sm
                                                                         text-slate-700">

                                                        {{ $varian->sku ?: '-' }}

                                                    </span>

                                                </td>


                                                {{-- HARGA BELI --}}
                                                <td class="px-6 py-5
                                                                   text-slate-700">

                                                    Rp {{ number_format(
                                    $hargaBeli,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                </td>


                                                {{-- HARGA JUAL --}}
                                                <td class="px-6 py-5
                                                                   font-semibold
                                                                   text-slate-900">

                                                    Rp {{ number_format(
                                    $hargaJual,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                </td>


                                                {{-- MARGIN --}}
                                                <td class="px-6 py-5">

                                                    <span class="font-semibold
                                                                {{ $margin > 0
                                    ? 'text-green-600'
                                    : (
                                        $margin < 0
                                        ? 'text-red-600'
                                        : 'text-slate-500'
                                    )
                                                                }}">

                                                        {{
                                    $margin > 0
                                    ? '+'
                                    : ''
                                                                }}

                                                        Rp {{ number_format(
                                    $margin,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                    </span>

                                                </td>


                                                {{-- STOK --}}
                                                <td class="px-6 py-5 text-center">

                                                    @if($stokMenipisVarian)

                                                        <div class="inline-flex
                                                                                items-center gap-2
                                                                                rounded-lg
                                                                                bg-red-50
                                                                                px-3 py-2">

                                                            <span class="font-bold
                                                                                     text-red-600">

                                                                {{ $varian->stok }}

                                                            </span>

                                                            <span class="text-xs
                                                                                     text-red-500">

                                                                Menipis

                                                            </span>

                                                        </div>

                                                    @else

                                                        <span class="inline-flex
                                                                                 rounded-lg
                                                                                 bg-green-50
                                                                                 px-3 py-2
                                                                                 font-bold
                                                                                 text-green-700">

                                                            {{ $varian->stok }}

                                                        </span>

                                                    @endif

                                                </td>


                                                {{-- MINIMUM STOK --}}
                                                <td class="px-6 py-5
                                                                   text-center
                                                                   text-slate-600">

                                                    {{ $varian->stok_minimum }}

                                                </td>


                                                {{-- AKSI --}}
                                                <td class="px-6 py-5">

                                                    <div class="flex
                                                                        justify-end gap-2">

                                                        {{-- EDIT --}}
                                                        <a href="{{ route(
                                    'varian.edit',
                                    $varian->id
                                ) }}" class="rounded-lg
                                                                           bg-amber-500
                                                                           px-4 py-2
                                                                           text-sm font-semibold
                                                                           text-white
                                                                           transition
                                                                           hover:bg-amber-600">
                                                            Edit
                                                        </a>


                                                        {{-- DELETE --}}
                                                        <form action="{{ route(
                                    'varian.destroy',
                                    $varian->id
                                ) }}" method="POST" onsubmit="
                                                                        return confirm(
                                                                            'Yakin ingin menghapus varian ini?'
                                                                        )
                                                                    ">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit" class="rounded-lg
                                                                               bg-red-600
                                                                               px-4 py-2
                                                                               text-sm font-semibold
                                                                               text-white
                                                                               transition
                                                                               hover:bg-red-700">
                                                                Hapus
                                                            </button>

                                                        </form>

                                                    </div>

                                                </td>

                                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


            @else

                {{-- EMPTY VARIAN --}}
                <div class="flex min-h-[350px]
                                flex-col items-center
                                justify-center px-6 py-16">

                    <div class="flex h-20 w-20
                                    items-center justify-center
                                    rounded-full bg-slate-100
                                    text-4xl">

                        🎨

                    </div>

                    <h3 class="mt-5 text-xl font-bold
                                   text-slate-900">

                        Belum Ada Varian

                    </h3>

                    <p class="mt-2 max-w-md text-center
                                  text-slate-500">

                        Barang ini belum memiliki varian.
                        Tambahkan warna, ukuran, SKU,
                        harga, dan informasi stok.

                    </p>

                    <a href="{{ route('varian.create', ['barang_id' => $barang->id]) }}" class="inline-flex items-center gap-2 px-5 py-3
                  bg-blue-600 hover:bg-blue-700
                  text-white font-semibold rounded-xl transition">

                        + Tambah Varian

                    </a>

                </div>

            @endif

        </div>

    </div>

@endsection