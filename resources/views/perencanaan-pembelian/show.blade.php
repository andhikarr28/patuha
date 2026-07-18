@extends('layouts.app')

@section('content')

@php

    $status = strtolower($perencanaan->status ?? 'draft');

    $statusClass = match ($status) {
        'draft' =>
            'bg-amber-50 text-amber-700 border-amber-200',

        'menunggu' =>
            'bg-blue-50 text-blue-700 border-blue-200',

        'sebagian' =>
            'bg-purple-50 text-purple-700 border-purple-200',

        'selesai' =>
            'bg-emerald-50 text-emerald-700 border-emerald-200',

        'dibatalkan' =>
            'bg-red-50 text-red-700 border-red-200',

        default =>
            'bg-slate-100 text-slate-700 border-slate-200'
    };

    $totalRencana =
        $perencanaan->details->sum('qty_rencana');

    $totalDiterima =
        $perencanaan->details->sum('qty_diterima');

    $estimasiTotal =
        $perencanaan->details->sum(function ($detail) {

            return
                $detail->qty_rencana *
                $detail->estimasi_harga;

        });

@endphp


<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div
        class="flex flex-col gap-4
               lg:flex-row
               lg:items-center
               lg:justify-between"
    >

        <div>

            <div
                class="flex flex-wrap
                       items-center
                       gap-3"
            >

                <h1
                    class="text-3xl
                           font-bold
                           text-slate-900"
                >
                    Detail Perencanaan
                </h1>

                <span
                    class="rounded-full
                           border
                           px-3 py-1
                           text-sm
                           font-semibold
                           {{ $statusClass }}"
                >
                    {{ ucfirst($status) }}
                </span>

            </div>


            <p class="mt-1 text-slate-500">

                Informasi lengkap perencanaan pembelian
                dan progres penerimaan barang.

            </p>

        </div>


        <div
            class="flex
                   flex-wrap
                   gap-3"
        >

            <a
                href="{{ route('perencanaan-pembelian.index') }}"
                class="rounded-xl
                       border
                       border-slate-300
                       bg-white
                       px-5 py-3
                       font-semibold
                       text-slate-700
                       transition
                       hover:bg-slate-50"
            >

                ← Kembali

            </a>


            @if(
                $status !== 'selesai' &&
                $status !== 'dibatalkan'
            )

                <a
                    href="{{ route(
                        'penerimaan-pembelian.create',
                        $perencanaan->id
                    ) }}"
                    class="rounded-xl
                           bg-blue-600
                           px-5 py-3
                           font-semibold
                           text-white
                           transition
                           hover:bg-blue-700"
                >

                    📥 Terima Barang

                </a>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INFO UTAMA --}}
    {{-- ========================================================= --}}

    <div
        class="grid
               grid-cols-1
               gap-5
               md:grid-cols-2
               xl:grid-cols-4"
    >

        {{-- NOMOR --}}

        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-medium
                       text-slate-500"
            >
                No. Perencanaan
            </p>

            <p
                class="mt-2
                       text-lg
                       font-bold
                       text-slate-900"
            >
                {{ $perencanaan->no_perencanaan }}
            </p>

        </div>


        {{-- TANGGAL --}}

        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-medium
                       text-slate-500"
            >
                Tanggal
            </p>

            <p
                class="mt-2
                       text-lg
                       font-bold
                       text-slate-900"
            >

                {{ \Carbon\Carbon::parse(
                    $perencanaan->tanggal_perencanaan
                )->format('d M Y') }}

            </p>

        </div>


        {{-- SUPPLIER --}}

        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-medium
                       text-slate-500"
            >
                Supplier
            </p>

            <p
                class="mt-2
                       text-lg
                       font-bold
                       text-slate-900"
            >

                {{ $perencanaan->supplier->nama_supplier
                    ?? '-' }}

            </p>

        </div>


        {{-- DIBUAT OLEH --}}

        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-medium
                       text-slate-500"
            >
                Dibuat Oleh
            </p>

            <p
                class="mt-2
                       text-lg
                       font-bold
                       text-slate-900"
            >

                {{ $perencanaan->user->name
                    ?? '-' }}

            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PROGRESS --}}
    {{-- ========================================================= --}}

    <div
        class="rounded-2xl
               border
               border-slate-200
               bg-white
               p-6"
    >

        <div
            class="flex
                   flex-col
                   gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Progress Penerimaan
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Perbandingan jumlah barang yang
                    direncanakan dengan yang sudah diterima.
                </p>

            </div>


            <div
                class="text-left
                       md:text-right"
            >

                <p
                    class="text-2xl
                           font-bold
                           text-blue-600"
                >

                    {{ $totalDiterima }}
                    /
                    {{ $totalRencana }}

                </p>

                <p
                    class="text-sm
                           text-slate-500"
                >
                    unit diterima
                </p>

            </div>

        </div>


        @php

            $progress =
                $totalRencana > 0
                    ? min(
                        100,
                        round(
                            ($totalDiterima / $totalRencana)
                            * 100
                        )
                    )
                    : 0;

        @endphp


        <div
            class="mt-5
                   h-3
                   overflow-hidden
                   rounded-full
                   bg-slate-100"
        >

            <div
                class="h-full
                       rounded-full
                       bg-blue-600
                       transition-all"
                style="width: {{ $progress }}%"
            ></div>

        </div>


        <div
            class="mt-2
                   flex
                   justify-between
                   text-sm
                   text-slate-500"
        >

            <span>
                {{ $progress }}% diterima
            </span>

            <span>
                Sisa:
                {{ max(
                    0,
                    $totalRencana - $totalDiterima
                ) }}
                unit
            </span>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DETAIL BARANG --}}
    {{-- ========================================================= --}}

    <div
        class="overflow-hidden
               rounded-2xl
               border
               border-slate-200
               bg-white"
    >

        <div
            class="flex
                   items-center
                   justify-between
                   border-b
                   border-slate-200
                   px-6 py-5"
        >

            <div>

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Daftar Barang
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Barang yang termasuk dalam
                    perencanaan pembelian ini.
                </p>

            </div>


            <span
                class="rounded-xl
                       bg-slate-100
                       px-4 py-2
                       text-sm
                       font-semibold
                       text-slate-600"
            >

                {{ $perencanaan->details->count() }}
                Varian

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead
                    class="bg-slate-50
                           text-left
                           text-sm
                           text-slate-500"
                >

                    <tr>

                        <th class="px-6 py-4">
                            Produk
                        </th>

                        <th class="px-6 py-4">
                            SKU
                        </th>

                        <th
                            class="px-6 py-4
                                   text-center"
                        >
                            Rencana
                        </th>

                        <th
                            class="px-6 py-4
                                   text-center"
                        >
                            Diterima
                        </th>

                        <th
                            class="px-6 py-4
                                   text-center"
                        >
                            Sisa
                        </th>

                        <th
                            class="px-6 py-4
                                   text-right"
                        >
                            Estimasi Harga
                        </th>

                        <th
                            class="px-6 py-4
                                   text-right"
                        >
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                    @forelse(
                        $perencanaan->details
                        as $detail
                    )

                        @php

                            $qtyRencana =
                                (int)
                                $detail->qty_rencana;

                            $qtyDiterima =
                                (int)
                                $detail->qty_diterima;

                            $sisa =
                                max(
                                    0,
                                    $qtyRencana -
                                    $qtyDiterima
                                );

                            $subtotal =
                                $qtyRencana *
                                $detail->estimasi_harga;

                        @endphp


                        <tr
                            class="transition
                                   hover:bg-slate-50"
                        >

                            {{-- PRODUK --}}

                            <td class="px-6 py-4">

                                <p
                                    class="font-semibold
                                           text-slate-900"
                                >

                                    {{
                                        $detail
                                            ->varian
                                            ->barang
                                            ->nama_barang
                                        ?? '-'
                                    }}

                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >

                                    {{
                                        $detail
                                            ->varian
                                            ->warna
                                        ?? '-'
                                    }}

                                    /

                                    {{
                                        $detail
                                            ->varian
                                            ->ukuran
                                        ?? '-'
                                    }}

                                </p>

                            </td>


                            {{-- SKU --}}

                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-600"
                            >

                                {{
                                    $detail
                                        ->varian
                                        ->sku
                                    ?? '-'
                                }}

                            </td>


                            {{-- RENCANA --}}

                            <td
                                class="px-6 py-4
                                       text-center
                                       font-semibold"
                            >

                                {{ $qtyRencana }}

                            </td>


                            {{-- DITERIMA --}}

                            <td
                                class="px-6 py-4
                                       text-center"
                            >

                                <span
                                    class="rounded-lg
                                           bg-emerald-50
                                           px-3 py-1
                                           font-semibold
                                           text-emerald-700"
                                >

                                    {{ $qtyDiterima }}

                                </span>

                            </td>


                            {{-- SISA --}}

                            <td
                                class="px-6 py-4
                                       text-center"
                            >

                                @if($sisa > 0)

                                    <span
                                        class="rounded-lg
                                               bg-amber-50
                                               px-3 py-1
                                               font-semibold
                                               text-amber-700"
                                    >

                                        {{ $sisa }}

                                    </span>

                                @else

                                    <span
                                        class="rounded-lg
                                               bg-emerald-50
                                               px-3 py-1
                                               font-semibold
                                               text-emerald-700"
                                    >
                                        Selesai
                                    </span>

                                @endif

                            </td>


                            {{-- HARGA --}}

                            <td
                                class="px-6 py-4
                                       text-right"
                            >

                                Rp
                                {{
                                    number_format(
                                        $detail
                                            ->estimasi_harga,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>


                            {{-- SUBTOTAL --}}

                            <td
                                class="px-6 py-4
                                       text-right
                                       font-bold
                                       text-slate-900"
                            >

                                Rp
                                {{
                                    number_format(
                                        $subtotal,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6
                                       py-16
                                       text-center"
                            >

                                <div
                                    class="text-4xl"
                                >
                                    📦
                                </div>

                                <p
                                    class="mt-3
                                           font-semibold
                                           text-slate-700"
                                >
                                    Tidak ada barang
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                {{-- TOTAL --}}

                @if(
                    $perencanaan
                        ->details
                        ->isNotEmpty()
                )

                    <tfoot
                        class="border-t
                               border-slate-200
                               bg-slate-50"
                    >

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-5
                                       text-right
                                       font-semibold
                                       text-slate-600"
                            >
                                Estimasi Total
                            </td>

                            <td
                                class="px-6 py-5
                                       text-right
                                       text-xl
                                       font-bold
                                       text-blue-600"
                            >

                                Rp
                                {{
                                    number_format(
                                        $estimasiTotal,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CATATAN --}}
    {{-- ========================================================= --}}

    @if($perencanaan->catatan)

        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-6"
        >

            <h2
                class="font-bold
                       text-slate-900"
            >
                📝 Catatan
            </h2>

            <p
                class="mt-3
                       whitespace-pre-line
                       text-slate-600"
            >
                {{ $perencanaan->catatan }}
            </p>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- INFORMASI ALUR --}}
    {{-- ========================================================= --}}

    @if(
        $status !== 'selesai' &&
        $status !== 'dibatalkan'
    )

        <div
            class="flex
                   items-start
                   gap-4
                   rounded-2xl
                   border
                   border-blue-200
                   bg-blue-50
                   p-5"
        >

            <div
                class="flex
                       h-12 w-12
                       shrink-0
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-100
                       text-xl"
            >
                📥
            </div>


            <div>

                <h3
                    class="font-bold
                           text-blue-900"
                >
                    Tahap Selanjutnya
                </h3>

                <p
                    class="mt-1
                           text-sm
                           leading-6
                           text-blue-700"
                >

                    Perencanaan ini belum selesai.
                    Gunakan menu

                    <strong>
                        Terima Barang
                    </strong>

                    ketika barang dari supplier tiba.

                    Stok barang hanya akan bertambah
                    setelah penerimaan dikonfirmasi.

                </p>

            </div>

        </div>

    @endif

</div>

@endsection