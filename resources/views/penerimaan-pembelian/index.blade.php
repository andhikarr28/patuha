@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Penerimaan Barang
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Proses penerimaan barang berdasarkan perencanaan pembelian
                yang telah dibuat.
            </p>
        </div>

        <a
            href="{{ route('perencanaan-pembelian.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl
                   bg-slate-800 px-5 py-3 text-sm font-semibold text-white
                   transition hover:bg-slate-700"
        >
            📋 Lihat Perencanaan
        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- FLASH MESSAGE --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">

            {{ session('error') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- INFORMASI --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">

        <div class="flex gap-4">

            <div
                class="flex h-11 w-11 shrink-0 items-center justify-center
                       rounded-xl bg-blue-100 text-xl"
            >
                📦
            </div>

            <div>

                <h2 class="font-semibold text-blue-900">
                    Proses Penerimaan Barang
                </h2>

                <p class="mt-1 text-sm leading-relaxed text-blue-700">

                    Pilih salah satu perencanaan pembelian di bawah ini
                    untuk mencatat barang yang telah diterima dari supplier.

                    Stok barang baru akan bertambah setelah proses
                    penerimaan berhasil dikonfirmasi.

                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DAFTAR PERENCANAAN --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl bg-white shadow-sm">

        {{-- Header Card --}}

        <div
            class="flex flex-col gap-4 border-b border-slate-100 p-6
                   md:flex-row md:items-center md:justify-between"
        >

            <div>

                <h2 class="text-xl font-bold text-slate-800">
                    Menunggu Penerimaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    Perencanaan yang masih memiliki barang
                    yang belum diterima seluruhnya.

                </p>

            </div>

            <div
                class="rounded-xl bg-slate-100 px-4 py-2
                       text-sm font-semibold text-slate-600"
            >

                {{ $perencanaan->count() }} Perencanaan

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- TABLE --}}
        {{-- ===================================================== --}}

        @if($perencanaan->count() > 0)

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr
                            class="border-b border-slate-100
                                   bg-slate-50 text-left"
                        >

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                No. Perencanaan
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                Tanggal
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                Supplier
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                Barang
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                Progress
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold
                                       uppercase tracking-wider text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs
                                       font-semibold uppercase tracking-wider
                                       text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach($perencanaan as $item)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | Hitung progress penerimaan
                                |--------------------------------------------------------------------------
                                */

                                $totalRencana =
                                    $item->details->sum('qty_rencana');

                                $totalDiterima =
                                    $item->details->sum('qty_diterima');

                                $totalSisa =
                                    max(
                                        0,
                                        $totalRencana - $totalDiterima
                                    );

                                $progress =
                                    $totalRencana > 0
                                        ? round(
                                            ($totalDiterima / $totalRencana)
                                            * 100
                                        )
                                        : 0;

                            @endphp


                            <tr class="transition hover:bg-slate-50">

                                {{-- Nomor Perencanaan --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="font-semibold text-slate-800"
                                    >

                                        {{ $item->no_perencanaan }}

                                    </div>

                                </td>


                                {{-- Tanggal --}}

                                <td class="px-6 py-5">

                                    <span class="text-sm text-slate-600">

                                        {{ \Carbon\Carbon::parse(
                                            $item->tanggal_perencanaan
                                        )->format('d M Y') }}

                                    </span>

                                </td>


                                {{-- Supplier --}}

                                <td class="px-6 py-5">

                                    <div class="font-medium text-slate-700">

                                        {{ $item->supplier->nama_supplier
                                            ?? '-' }}

                                    </div>

                                </td>


                                {{-- Jumlah Barang --}}

                                <td class="px-6 py-5">

                                    <div class="space-y-1">

                                        <div
                                            class="font-semibold text-slate-700"
                                        >

                                            {{ $item->details->count() }}
                                            Varian

                                        </div>

                                        <div class="text-xs text-slate-500">

                                            Total rencana:
                                            {{ $totalRencana }} unit

                                        </div>

                                    </div>

                                </td>


                                {{-- Progress --}}

                                <td class="min-w-[220px] px-6 py-5">

                                    <div class="space-y-2">

                                        <div
                                            class="flex items-center
                                                   justify-between text-xs"
                                        >

                                            <span class="text-slate-500">

                                                {{ $totalDiterima }}
                                                /
                                                {{ $totalRencana }}
                                                diterima

                                            </span>

                                            <span
                                                class="font-semibold
                                                       text-slate-700"
                                            >

                                                {{ $progress }}%

                                            </span>

                                        </div>


                                        <div
                                            class="h-2 overflow-hidden
                                                   rounded-full bg-slate-200"
                                        >

                                            <div
                                                class="h-full rounded-full
                                                       bg-blue-600
                                                       transition-all"
                                                style="width:
                                                    {{ min(
                                                        100,
                                                        $progress
                                                    ) }}%"
                                            >
                                            </div>

                                        </div>


                                        @if($totalSisa > 0)

                                            <div
                                                class="text-xs
                                                       text-orange-600"
                                            >

                                                Sisa {{ $totalSisa }} unit

                                            </div>

                                        @endif

                                    </div>

                                </td>


                                {{-- Status --}}

                                <td class="px-6 py-5">

                                    @if(
                                        $item->status ===
                                        'sebagian_diterima'
                                    )

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-orange-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-orange-700"
                                        >

                                            Sebagian Diterima

                                        </span>

                                    @elseif(
                                        $item->status ===
                                        'dipesan'
                                    )

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-blue-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-blue-700"
                                        >

                                            Dipesan

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-slate-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-slate-700"
                                        >

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $item->status
                                                )
                                            ) }}

                                        </span>

                                    @endif

                                </td>


                                {{-- Aksi --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="flex justify-end gap-2"
                                    >

                                        {{-- Detail Perencanaan --}}

                                        <a
                                            href="{{ route(
                                                'perencanaan-pembelian.show',
                                                $item
                                            ) }}"
                                            class="rounded-lg border
                                                   border-slate-200 px-3 py-2
                                                   text-sm font-medium
                                                   text-slate-600 transition
                                                   hover:bg-slate-100"
                                        >

                                            Detail

                                        </a>


                                        {{-- Proses Penerimaan --}}

                                        <a
                                            href="{{ route(
                                                'penerimaan-pembelian.create',
                                                $item
                                            ) }}"
                                            class="rounded-lg bg-blue-600
                                                   px-4 py-2 text-sm
                                                   font-semibold text-white
                                                   transition
                                                   hover:bg-blue-700"
                                        >

                                            📦 Terima Barang

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


        @else

            {{-- ================================================= --}}
            {{-- EMPTY STATE --}}
            {{-- ================================================= --}}

            <div class="px-6 py-16 text-center">

                <div
                    class="mx-auto flex h-20 w-20 items-center
                           justify-center rounded-full bg-slate-100
                           text-4xl"
                >

                    📦

                </div>

                <h3
                    class="mt-5 text-lg font-bold text-slate-800"
                >

                    Tidak Ada Penerimaan Tertunda

                </h3>

                <p
                    class="mx-auto mt-2 max-w-md text-sm
                           leading-relaxed text-slate-500"
                >

                    Belum ada perencanaan pembelian yang menunggu
                    proses penerimaan barang.

                </p>

                <a
                    href="{{ route(
                        'perencanaan-pembelian.index'
                    ) }}"
                    class="mt-6 inline-flex rounded-xl bg-blue-600
                           px-5 py-3 text-sm font-semibold text-white
                           transition hover:bg-blue-700"
                >

                    Lihat Perencanaan Pembelian

                </a>

            </div>

        @endif

    </div>

</div>

@endsection