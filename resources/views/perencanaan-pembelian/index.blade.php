@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Perencanaan Pembelian
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Buat dan kelola rencana pembelian barang dari supplier.
            </p>
        </div>

        <a
            href="{{ route('perencanaan-pembelian.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl
                   bg-blue-600 px-5 py-3 text-sm font-semibold text-white
                   transition hover:bg-blue-700"
        >
            + Buat Perencanaan
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
    {{-- SUMMARY CARD --}}
    {{-- ========================================================= --}}

    @php

        $jumlahDraft =
            $perencanaan->where('status', 'draft')->count();

        $jumlahDipesan =
            $perencanaan->where('status', 'dipesan')->count();

        $jumlahSebagian =
            $perencanaan
                ->where('status', 'sebagian_diterima')
                ->count();

        $jumlahSelesai =
            $perencanaan->where('status', 'selesai')->count();

    @endphp


    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- DRAFT --}}

        <div class="rounded-2xl bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Draft
                    </p>

                    <h2 class="mt-1 text-3xl font-bold text-slate-800">
                        {{ $jumlahDraft }}
                    </h2>

                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-slate-100 text-xl"
                >
                    📝
                </div>

            </div>

        </div>


        {{-- DIPESAN --}}

        <div class="rounded-2xl bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Menunggu Penerimaan
                    </p>

                    <h2 class="mt-1 text-3xl font-bold text-blue-600">
                        {{ $jumlahDipesan }}
                    </h2>

                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-blue-50 text-xl"
                >
                    🚚
                </div>

            </div>

        </div>


        {{-- SEBAGIAN --}}

        <div class="rounded-2xl bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Diterima Sebagian
                    </p>

                    <h2 class="mt-1 text-3xl font-bold text-orange-600">
                        {{ $jumlahSebagian }}
                    </h2>

                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-orange-50 text-xl"
                >
                    📦
                </div>

            </div>

        </div>


        {{-- SELESAI --}}

        <div class="rounded-2xl bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Selesai
                    </p>

                    <h2 class="mt-1 text-3xl font-bold text-green-600">
                        {{ $jumlahSelesai }}
                    </h2>

                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-green-50 text-xl"
                >
                    ✓
                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INFO ALUR --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">

        <div class="flex gap-4">

            <div
                class="flex h-11 w-11 shrink-0 items-center justify-center
                       rounded-xl bg-blue-100 text-xl"
            >
                💡
            </div>

            <div>

                <h2 class="font-semibold text-blue-900">
                    Alur Pembelian Barang
                </h2>

                <p class="mt-1 text-sm leading-relaxed text-blue-700">

                    Buat perencanaan barang yang akan dibeli dari supplier.
                    Perencanaan tidak langsung menambah stok.

                    Stok baru bertambah ketika barang benar-benar diterima
                    melalui proses penerimaan barang.

                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DAFTAR PERENCANAAN --}}
    {{-- ========================================================= --}}

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">

        {{-- HEADER TABLE --}}

        <div
            class="flex flex-col gap-4 border-b border-slate-100 p-6
                   md:flex-row md:items-center md:justify-between"
        >

            <div>

                <h2 class="text-xl font-bold text-slate-800">
                    Daftar Perencanaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Riwayat dan status seluruh perencanaan pembelian.
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
                                Estimasi
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

                                $totalQty =
                                    $item->details
                                        ->sum('qty_rencana');

                                $totalEstimasi =
                                    $item->details
                                        ->sum(function ($detail) {

                                            return
                                                $detail->qty_rencana
                                                *
                                                $detail->estimasi_harga;

                                        });

                            @endphp


                            <tr class="transition hover:bg-slate-50">

                                {{-- NOMOR --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="font-semibold text-slate-800"
                                    >
                                        {{ $item->no_perencanaan }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">

                                        Dibuat oleh:
                                        {{ $item->user->name ?? '-' }}

                                    </div>

                                </td>


                                {{-- TANGGAL --}}

                                <td class="px-6 py-5">

                                    <span class="text-sm text-slate-600">

                                        {{ \Carbon\Carbon::parse(
                                            $item->tanggal_perencanaan
                                        )->format('d M Y') }}

                                    </span>

                                </td>


                                {{-- SUPPLIER --}}

                                <td class="px-6 py-5">

                                    <div class="font-medium text-slate-700">

                                        {{ $item->supplier->nama_supplier
                                            ?? '-' }}

                                    </div>

                                </td>


                                {{-- BARANG --}}

                                <td class="px-6 py-5">

                                    <div class="space-y-1">

                                        <div
                                            class="font-semibold
                                                   text-slate-700"
                                        >

                                            {{ $item->details->count() }}
                                            Varian

                                        </div>

                                        <div class="text-xs text-slate-500">

                                            {{ $totalQty }} unit direncanakan

                                        </div>

                                    </div>

                                </td>


                                {{-- ESTIMASI --}}

                                <td class="px-6 py-5">

                                    <span
                                        class="font-semibold text-slate-700"
                                    >

                                        Rp
                                        {{ number_format(
                                            $totalEstimasi,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td class="px-6 py-5">

                                    @if($item->status === 'draft')

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-slate-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-slate-700"
                                        >
                                            Draft
                                        </span>


                                    @elseif($item->status === 'dipesan')

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-blue-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-blue-700"
                                        >
                                            Dipesan
                                        </span>


                                    @elseif(
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


                                    @elseif($item->status === 'selesai')

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-green-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-green-700"
                                        >
                                            Selesai
                                        </span>


                                    @elseif($item->status === 'dibatalkan')

                                        <span
                                            class="inline-flex rounded-full
                                                   bg-red-100 px-3 py-1
                                                   text-xs font-semibold
                                                   text-red-700"
                                        >
                                            Dibatalkan
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


                                {{-- AKSI --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="flex flex-wrap
                                               justify-end gap-2"
                                    >

                                        {{-- DETAIL --}}

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


                                        {{-- TERIMA BARANG --}}

                                        @if(
                                            in_array(
                                                $item->status,
                                                [
                                                    'dipesan',
                                                    'sebagian_diterima'
                                                ]
                                            )
                                        )

                                            <a
                                                href="{{ route(
                                                    'penerimaan-pembelian.create',
                                                    $item
                                                ) }}"
                                                class="rounded-lg
                                                       bg-blue-600 px-3 py-2
                                                       text-sm font-semibold
                                                       text-white transition
                                                       hover:bg-blue-700"
                                            >
                                                📦 Terima
                                            </a>

                                        @endif


                                        {{-- BATALKAN --}}

                                        @if(
                                            in_array(
                                                $item->status,
                                                [
                                                    'draft',
                                                    'dipesan'
                                                ]
                                            )
                                        )

                                            <form
                                                action="{{ route(
                                                    'perencanaan-pembelian.cancel',
                                                    $item
                                                ) }}"
                                                method="POST"
                                                onsubmit="
                                                    return confirm(
                                                        'Batalkan perencanaan ini?'
                                                    )
                                                "
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-red-50 px-3 py-2
                                                           text-sm font-semibold
                                                           text-red-600
                                                           transition
                                                           hover:bg-red-100"
                                                >
                                                    Batalkan
                                                </button>

                                            </form>

                                        @endif

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

            <div class="px-6 py-20 text-center">

                <div
                    class="mx-auto flex h-20 w-20 items-center
                           justify-center rounded-full bg-slate-100
                           text-4xl"
                >
                    📋
                </div>

                <h3 class="mt-5 text-lg font-bold text-slate-800">

                    Belum Ada Perencanaan Pembelian

                </h3>

                <p
                    class="mx-auto mt-2 max-w-md text-sm
                           leading-relaxed text-slate-500"
                >

                    Buat perencanaan pembelian untuk menentukan
                    barang dan jumlah yang akan dipesan dari supplier.

                </p>

                <a
                    href="{{ route(
                        'perencanaan-pembelian.create'
                    ) }}"
                    class="mt-6 inline-flex rounded-xl bg-blue-600
                           px-5 py-3 text-sm font-semibold text-white
                           transition hover:bg-blue-700"
                >
                    + Buat Perencanaan Pertama
                </a>

            </div>

        @endif

    </div>

</div>

@endsection