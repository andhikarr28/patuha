@extends('layouts.app')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>

                <a href="{{ route('penerimaan-pembelian.index') }}" class="mb-3 inline-flex items-center gap-2
                           text-sm font-medium text-slate-500
                           hover:text-slate-800">
                    ← Kembali ke Penerimaan
                </a>

                <h1 class="text-3xl font-bold text-slate-800">
                    Detail Penerimaan Barang
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi transaksi barang yang telah diterima dari supplier.
                </p>

            </div>


            <div>

                <span class="inline-flex items-center gap-2 rounded-full
                           bg-green-100 px-4 py-2
                           text-sm font-semibold text-green-700">
                    ✓ Diterima
                </span>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ========================================================= --}}

        @if(session('success'))

            <div class="rounded-xl border border-green-200
                           bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- INFORMASI UTAMA --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">


            {{-- ===================================================== --}}
            {{-- INFORMASI PENERIMAAN --}}
            {{-- ===================================================== --}}

            <div class="rounded-2xl bg-white p-6 shadow-sm xl:col-span-2">

                <div class="mb-6 flex items-center justify-between">

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Informasi Penerimaan
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Dokumen transaksi penerimaan barang.
                        </p>

                    </div>

                    <div class="rounded-xl bg-blue-50
                               px-4 py-2 text-sm
                               font-bold text-blue-700">
                        {{ $pembelian->no_faktur }}
                    </div>

                </div>


                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    {{-- NO FAKTUR --}}

                    <div>

                        <p class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400">
                            No. Faktur / Surat Jalan
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">

                            {{ $pembelian->no_faktur }}

                        </p>

                    </div>


                    {{-- TANGGAL --}}

                    <div>

                        <p class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400">
                            Tanggal Penerimaan
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">

                            {{ \Carbon\Carbon::parse(
        $pembelian->tanggal_pembelian
    )->format('d M Y') }}

                        </p>

                    </div>


                    {{-- SUPPLIER --}}

                    <div>

                        <p class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400">
                            Supplier
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">

                            {{ $pembelian->supplier->nama_supplier ?? '-' }}

                        </p>

                    </div>


                    {{-- USER --}}

                    <div>

                        <p class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400">
                            Diterima Oleh
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">

                            {{ $pembelian->user->name ?? '-' }}

                        </p>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- REFERENSI PERENCANAAN --}}
            {{-- ===================================================== --}}

            <div class="rounded-2xl bg-white p-6 shadow-sm">

                <div class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-purple-100 text-xl">
                    📋
                </div>

                <h2 class="mt-4 font-bold text-slate-800">
                    Perencanaan Pembelian
                </h2>

                @if($pembelian->perencanaan)

                            <p class="mt-1 text-sm text-slate-500">
                                Penerimaan ini berasal dari:
                            </p>

                            <div class="mt-4 rounded-xl border
                                           border-purple-100 bg-purple-50 p-4">

                                <p class="text-xs font-semibold uppercase
                                               tracking-wide text-purple-500">
                                    No. Perencanaan
                                </p>

                                <p class="mt-1 font-bold text-purple-800">
                                    {{ $pembelian->perencanaan->no_perencanaan }}
                                </p>

                                <p class="mt-2 text-xs text-purple-600">

                                    {{ \Carbon\Carbon::parse(
                        $pembelian
                            ->perencanaan
                            ->tanggal_perencanaan
                    )->format('d M Y') }}

                                </p>

                            </div>


                            <a href="{{ route(
                        'perencanaan-pembelian.show',
                        $pembelian->perencanaan
                    ) }}" class="mt-4 inline-flex w-full
                                           items-center justify-center
                                           rounded-xl border border-slate-200
                                           px-4 py-3 text-sm font-semibold
                                           text-slate-600 transition
                                           hover:bg-slate-50">
                                Lihat Perencanaan
                            </a>

                @else

                    <p class="mt-3 text-sm text-slate-500">
                        Tidak memiliki referensi perencanaan.
                    </p>

                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DETAIL BARANG --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b
                       border-slate-100 p-6
                       md:flex-row md:items-center
                       md:justify-between">

                <div>

                    <h2 class="text-xl font-bold text-slate-800">
                        Barang yang Diterima
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Rincian barang yang masuk pada penerimaan ini.
                    </p>

                </div>

                <div class="rounded-xl bg-slate-100
                           px-4 py-2 text-sm
                           font-semibold text-slate-600">
                    {{ $pembelian->detailPembelian->count() }}
                    Varian
                </div>

            </div>


            @if($pembelian->detailPembelian->count() > 0)

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead>

                            <tr class="border-b border-slate-100
                                           bg-slate-50 text-left">

                                <th class="px-6 py-4 text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Barang
                                </th>

                                <th class="px-6 py-4 text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    SKU
                                </th>

                                <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Qty
                                </th>

                                <th class="px-6 py-4 text-right text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Harga Satuan
                                </th>

                                <th class="px-6 py-4 text-right text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Brutto
                                </th>

                                <th class="px-6 py-4 text-right text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Diskon
                                </th>

                                <th class="px-6 py-4 text-right text-xs
                                               font-semibold uppercase
                                               tracking-wider text-slate-500">
                                    Netto
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach($pembelian->detailPembelian as $detail)

                                            @php

                                                $brutto =
                                                    $detail->qty
                                                    *
                                                    $detail->harga_satuan;

                                                $netto =
                                                    max(
                                                        0,
                                                        $brutto
                                                        -
                                                        $detail->diskon
                                                    );

                                            @endphp


                                            <tr class="hover:bg-slate-50">

                                                {{-- BARANG --}}

                                                <td class="px-6 py-5">

                                                    <div class="flex items-center gap-4">

                                                        <div class="flex h-12 w-12
                                                                           shrink-0 items-center
                                                                           justify-center
                                                                           rounded-xl bg-slate-100
                                                                           text-xl">
                                                            📦
                                                        </div>


                                                        <div>

                                                            <p class="font-semibold
                                                                               text-slate-800">

                                                                {{ $detail->varian
                                    ->barang
                                    ->nama_barang
                                    ?? 'Barang' }}

                                                            </p>

                                                            <p class="mt-1 text-sm
                                                                               text-slate-500">

                                                                {{ $detail->varian
                                    ->warna ?? '-' }}

                                                                @if(
                                                                                                $detail->varian
                                                                                                    ->ukuran
                                                                                            )

                                                                                            /

                                                                                            {{ $detail->varian
                                                                    ->ukuran }}

                                                                @endif

                                                            </p>

                                                        </div>

                                                    </div>

                                                </td>


                                                {{-- SKU --}}

                                                <td class="px-6 py-5">

                                                    <span class="rounded-lg bg-slate-100
                                                                       px-3 py-1 text-xs
                                                                       font-semibold
                                                                       text-slate-600">

                                                        {{ $detail->varian->sku ?? '-' }}

                                                    </span>

                                                </td>


                                                {{-- QTY --}}

                                                <td class="px-6 py-5 text-center">

                                                    <span class="inline-flex min-w-[50px]
                                                                       justify-center rounded-lg
                                                                       bg-green-100 px-3 py-2
                                                                       font-bold text-green-700">
                                                        +{{ $detail->qty }}
                                                    </span>

                                                </td>


                                                {{-- HARGA --}}

                                                <td class="px-6 py-5 text-right
                                                                   text-sm text-slate-600">

                                                    Rp{{ number_format(
                                    $detail->harga_satuan,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                </td>


                                                {{-- BRUTTO --}}

                                                <td class="px-6 py-5 text-right
                                                                   font-medium text-slate-700">

                                                    Rp{{ number_format(
                                    $brutto,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                </td>


                                                {{-- DISKON --}}

                                                <td class="px-6 py-5 text-right
                                                                   text-sm text-red-600">

                                                    @if($detail->diskon > 0)

                                                                        - Rp{{ number_format(
                                                            $detail->diskon,
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) }}

                                                    @else

                                                        Rp0

                                                    @endif

                                                </td>


                                                {{-- NETTO --}}

                                                <td class="px-6 py-5 text-right
                                                                   font-bold text-slate-800">

                                                    Rp{{ number_format(
                                    $netto,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                                </td>

                                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-12 text-center text-slate-500">
                    Tidak ada detail barang pada penerimaan ini.
                </div>

            @endif

        </div>


        {{-- ========================================================= --}}
        {{-- RINGKASAN TOTAL --}}
        {{-- ========================================================= --}}

        <div class="flex justify-end">

            <div class="w-full rounded-2xl bg-white
                       p-6 shadow-sm md:w-[420px]">

                <h2 class="text-lg font-bold text-slate-800">
                    Ringkasan Pembelian
                </h2>


                <div class="mt-5 space-y-4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Total Brutto
                        </span>

                        <span class="font-semibold text-slate-700">

                            Rp{{ number_format(
        $pembelian->total_brutto,
        0,
        ',',
        '.'
    ) }}

                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Total Diskon
                        </span>

                        <span class="font-semibold text-red-600">

                            - Rp{{ number_format(
        $pembelian->total_diskon,
        0,
        ',',
        '.'
    ) }}

                        </span>

                    </div>


                    <div class="border-t border-slate-100 pt-4">

                        <div class="flex items-center justify-between">

                            <span class="font-bold text-slate-800">
                                Total Netto
                            </span>

                            <span class="text-2xl font-bold text-blue-600">

                                Rp{{ number_format(
        $pembelian->total_netto,
        0,
        ',',
        '.'
    ) }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- INFO STOK --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border border-green-100
                   bg-green-50 p-5">

            <div class="flex gap-4">

                <div class="flex h-11 w-11 shrink-0
                           items-center justify-center
                           rounded-xl bg-green-100 text-xl">
                    ✓
                </div>

                <div>

                    <h3 class="font-bold text-green-800">
                        Penerimaan Telah Diproses
                    </h3>

                    <p class="mt-1 text-sm leading-relaxed
                               text-green-700">
                        Barang pada transaksi ini telah ditambahkan
                        ke stok sistem. Data penerimaan ini menjadi
                        bagian dari riwayat pembelian aktual.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection