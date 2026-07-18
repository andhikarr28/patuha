@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>

            <a
                href="{{ route('penerimaan-pembelian.index') }}"
                class="mb-3 inline-flex items-center gap-2 text-sm
                       font-medium text-slate-500 hover:text-slate-800"
            >
                ← Kembali ke Penerimaan
            </a>

            <h1 class="text-3xl font-bold text-slate-800">
                Penerimaan Barang
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Catat barang yang benar-benar diterima dari supplier.
            </p>

        </div>


        {{-- STATUS --}}

        <div>

            @if($perencanaan->status === 'sebagian_diterima')

                <span
                    class="inline-flex rounded-full bg-orange-100
                           px-4 py-2 text-sm font-semibold text-orange-700"
                >
                    Sebagian Diterima
                </span>

            @elseif($perencanaan->status === 'dipesan')

                <span
                    class="inline-flex rounded-full bg-blue-100
                           px-4 py-2 text-sm font-semibold text-blue-700"
                >
                    Dipesan
                </span>

            @else

                <span
                    class="inline-flex rounded-full bg-slate-100
                           px-4 py-2 text-sm font-semibold text-slate-700"
                >
                    {{ ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $perencanaan->status
                        )
                    ) }}
                </span>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if(session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-5">

            <h3 class="font-semibold text-red-800">
                Penerimaan belum dapat disimpan
            </h3>

            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route(
            'penerimaan-pembelian.store',
            $perencanaan
        ) }}"
        method="POST"
        id="formPenerimaan"
    >

        @csrf


        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ================================================= --}}
            {{-- KOLOM KIRI --}}
            {{-- ================================================= --}}

            <div class="space-y-6 xl:col-span-2">


                {{-- ============================================= --}}
                {{-- INFORMASI PERENCANAAN --}}
                {{-- ============================================= --}}

                <div class="rounded-2xl bg-white p-6 shadow-sm">

                    <div class="mb-5 flex items-center justify-between">

                        <div>

                            <h2 class="text-lg font-bold text-slate-800">
                                Informasi Perencanaan
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Referensi rencana pembelian barang.
                            </p>

                        </div>

                        <span
                            class="rounded-lg bg-slate-100 px-3 py-2
                                   text-sm font-semibold text-slate-700"
                        >
                            {{ $perencanaan->no_perencanaan }}
                        </span>

                    </div>


                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

                        {{-- Tanggal --}}

                        <div>

                            <p class="text-xs font-semibold uppercase
                                      tracking-wide text-slate-400">
                                Tanggal Rencana
                            </p>

                            <p class="mt-2 font-semibold text-slate-700">

                                {{ \Carbon\Carbon::parse(
                                    $perencanaan->tanggal_perencanaan
                                )->format('d M Y') }}

                            </p>

                        </div>


                        {{-- Supplier --}}

                        <div>

                            <p class="text-xs font-semibold uppercase
                                      tracking-wide text-slate-400">
                                Supplier
                            </p>

                            <p class="mt-2 font-semibold text-slate-700">

                                {{ $perencanaan->supplier->nama_supplier
                                    ?? '-' }}

                            </p>

                        </div>


                        {{-- Jumlah Varian --}}

                        <div>

                            <p class="text-xs font-semibold uppercase
                                      tracking-wide text-slate-400">
                                Barang Tersisa
                            </p>

                            <p class="mt-2 font-semibold text-slate-700">

                                {{ $perencanaan->details->count() }}
                                Varian

                            </p>

                        </div>

                    </div>


                    @if($perencanaan->catatan)

                        <div
                            class="mt-5 rounded-xl border border-slate-100
                                   bg-slate-50 p-4"
                        >

                            <p class="text-xs font-semibold uppercase
                                      tracking-wide text-slate-400">
                                Catatan
                            </p>

                            <p class="mt-2 text-sm text-slate-600">
                                {{ $perencanaan->catatan }}
                            </p>

                        </div>

                    @endif

                </div>


                {{-- ============================================= --}}
                {{-- DAFTAR BARANG --}}
                {{-- ============================================= --}}

                <div class="rounded-2xl bg-white shadow-sm">

                    <div class="border-b border-slate-100 p-6">

                        <h2 class="text-lg font-bold text-slate-800">
                            Barang yang Diterima
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">

                            Isi jumlah barang yang datang sekarang.

                            Qty tidak boleh melebihi sisa
                            perencanaan.

                        </p>

                    </div>


                    <div class="divide-y divide-slate-100">

                        @foreach($perencanaan->details as $index => $detail)

                            @php

                                $sisa =
                                    max(
                                        0,
                                        $detail->qty_rencana
                                        -
                                        $detail->qty_diterima
                                    );

                                $hargaDefault =
                                    old(
                                        "items.$index.harga_satuan",
                                        $detail->estimasi_harga ?? 0
                                    );

                            @endphp


                            <div
                                class="p-6"
                                data-item-row
                            >

                                {{-- Hidden Detail ID --}}

                                <input
                                    type="hidden"
                                    name="items[{{ $index }}][detail_perencanaan_id]"
                                    value="{{ $detail->id }}"
                                >


                                {{-- ================================= --}}
                                {{-- INFORMASI PRODUK --}}
                                {{-- ================================= --}}

                                <div
                                    class="flex flex-col gap-5
                                           lg:flex-row lg:items-start
                                           lg:justify-between"
                                >

                                    <div class="flex gap-4">

                                        {{-- Icon / Foto Placeholder --}}

                                        <div
                                            class="flex h-16 w-16 shrink-0
                                                   items-center justify-center
                                                   rounded-xl bg-slate-100
                                                   text-2xl"
                                        >
                                            📦
                                        </div>


                                        <div>

                                            <h3
                                                class="font-bold text-slate-800"
                                            >

                                                {{ $detail->varian->barang
                                                    ->nama_barang
                                                    ?? 'Barang' }}

                                            </h3>


                                            <div
                                                class="mt-1 flex flex-wrap
                                                       gap-2 text-sm
                                                       text-slate-500"
                                            >

                                                @if($detail->varian->warna)

                                                    <span>
                                                        {{ $detail->varian->warna }}
                                                    </span>

                                                @endif


                                                @if($detail->varian->ukuran)

                                                    <span>•</span>

                                                    <span>
                                                        {{ $detail->varian->ukuran }}
                                                    </span>

                                                @endif

                                            </div>


                                            @if($detail->varian->sku)

                                                <div
                                                    class="mt-2 inline-flex
                                                           rounded-md
                                                           bg-slate-100
                                                           px-2 py-1
                                                           text-xs font-medium
                                                           text-slate-500"
                                                >
                                                    SKU:
                                                    {{ $detail->varian->sku }}
                                                </div>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- Progress Qty --}}

                                    <div
                                        class="grid grid-cols-3 gap-3
                                               text-center"
                                    >

                                        <div
                                            class="min-w-[80px] rounded-xl
                                                   bg-slate-50 px-3 py-3"
                                        >

                                            <p
                                                class="text-xs text-slate-400"
                                            >
                                                Rencana
                                            </p>

                                            <p
                                                class="mt-1 text-lg font-bold
                                                       text-slate-700"
                                            >
                                                {{ $detail->qty_rencana }}
                                            </p>

                                        </div>


                                        <div
                                            class="min-w-[80px] rounded-xl
                                                   bg-green-50 px-3 py-3"
                                        >

                                            <p
                                                class="text-xs text-green-600"
                                            >
                                                Diterima
                                            </p>

                                            <p
                                                class="mt-1 text-lg font-bold
                                                       text-green-700"
                                            >
                                                {{ $detail->qty_diterima }}
                                            </p>

                                        </div>


                                        <div
                                            class="min-w-[80px] rounded-xl
                                                   bg-orange-50 px-3 py-3"
                                        >

                                            <p
                                                class="text-xs text-orange-600"
                                            >
                                                Sisa
                                            </p>

                                            <p
                                                class="mt-1 text-lg font-bold
                                                       text-orange-700"
                                            >
                                                {{ $sisa }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- ================================= --}}
                                {{-- INPUT PENERIMAAN --}}
                                {{-- ================================= --}}

                                <div
                                    class="mt-6 grid grid-cols-1 gap-4
                                           md:grid-cols-3"
                                >

                                    {{-- Qty Diterima --}}

                                    <div>

                                        <label
                                            class="mb-2 block text-sm
                                                   font-semibold text-slate-700"
                                        >
                                            Diterima Sekarang
                                        </label>

                                        <input
                                            type="number"
                                            name="items[{{ $index }}][qty_diterima]"
                                            value="{{ old(
                                                "items.$index.qty_diterima",
                                                0
                                            ) }}"
                                            min="0"
                                            max="{{ $sisa }}"
                                            data-qty
                                            class="w-full rounded-xl border
                                                   border-slate-200 px-4 py-3
                                                   outline-none transition
                                                   focus:border-blue-500
                                                   focus:ring-2
                                                   focus:ring-blue-100"
                                        >

                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            Maksimal {{ $sisa }} unit
                                        </p>

                                    </div>


                                    {{-- Harga Aktual --}}

                                    <div>

                                        <label
                                            class="mb-2 block text-sm
                                                   font-semibold text-slate-700"
                                        >
                                            Harga Satuan Aktual
                                        </label>

                                        <input
                                            type="number"
                                            name="items[{{ $index }}][harga_satuan]"
                                            value="{{ $hargaDefault }}"
                                            min="0"
                                            step="0.01"
                                            data-harga
                                            class="w-full rounded-xl border
                                                   border-slate-200 px-4 py-3
                                                   outline-none transition
                                                   focus:border-blue-500
                                                   focus:ring-2
                                                   focus:ring-blue-100"
                                        >

                                        @if($detail->estimasi_harga)

                                            <p
                                                class="mt-1 text-xs
                                                       text-slate-400"
                                            >

                                                Estimasi:
                                                Rp{{ number_format(
                                                    $detail->estimasi_harga,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            </p>

                                        @endif

                                    </div>


                                    {{-- Diskon --}}

                                    <div>

                                        <label
                                            class="mb-2 block text-sm
                                                   font-semibold text-slate-700"
                                        >
                                            Diskon
                                        </label>

                                        <input
                                            type="number"
                                            name="items[{{ $index }}][diskon]"
                                            value="{{ old(
                                                "items.$index.diskon",
                                                0
                                            ) }}"
                                            min="0"
                                            step="0.01"
                                            data-diskon
                                            class="w-full rounded-xl border
                                                   border-slate-200 px-4 py-3
                                                   outline-none transition
                                                   focus:border-blue-500
                                                   focus:ring-2
                                                   focus:ring-blue-100"
                                        >

                                        <p
                                            class="mt-1 text-xs
                                                   text-slate-400"
                                        >
                                            Diskon total untuk item ini
                                        </p>

                                    </div>

                                </div>


                                {{-- Preview Subtotal --}}

                                <div
                                    class="mt-5 flex items-center
                                           justify-between rounded-xl
                                           bg-slate-50 px-4 py-3"
                                >

                                    <span class="text-sm text-slate-500">
                                        Total Item
                                    </span>

                                    <span
                                        class="font-bold text-slate-800"
                                        data-subtotal
                                    >
                                        Rp0
                                    </span>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- KOLOM KANAN --}}
            {{-- ================================================= --}}

            <div>

                <div
                    class="sticky top-6 space-y-6"
                >

                    {{-- ========================================= --}}
                    {{-- INFORMASI PENERIMAAN --}}
                    {{-- ========================================= --}}

                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-bold text-slate-800">
                            Informasi Penerimaan
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan dokumen penerimaan dari supplier.
                        </p>


                        <div class="mt-6 space-y-5">

                            {{-- No Faktur --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm
                                           font-semibold text-slate-700"
                                >
                                    No. Faktur / Surat Jalan
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="no_faktur"
                                    value="{{ old('no_faktur') }}"
                                    placeholder="Contoh: INV-2026-001"
                                    required
                                    class="w-full rounded-xl border
                                           border-slate-200 px-4 py-3
                                           outline-none transition
                                           focus:border-blue-500
                                           focus:ring-2
                                           focus:ring-blue-100"
                                >

                            </div>


                            {{-- Tanggal --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm
                                           font-semibold text-slate-700"
                                >
                                    Tanggal Penerimaan
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_pembelian"
                                    value="{{ old(
                                        'tanggal_pembelian',
                                        now()->format('Y-m-d')
                                    ) }}"
                                    required
                                    class="w-full rounded-xl border
                                           border-slate-200 px-4 py-3
                                           outline-none transition
                                           focus:border-blue-500
                                           focus:ring-2
                                           focus:ring-blue-100"
                                >

                            </div>

                        </div>

                    </div>


                    {{-- ========================================= --}}
                    {{-- RINGKASAN --}}
                    {{-- ========================================= --}}

                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-bold text-slate-800">
                            Ringkasan Penerimaan
                        </h2>


                        <div class="mt-5 space-y-4">

                            <div
                                class="flex items-center justify-between
                                       text-sm"
                            >

                                <span class="text-slate-500">
                                    Total Qty Diterima
                                </span>

                                <span
                                    id="totalQty"
                                    class="font-semibold text-slate-800"
                                >
                                    0 unit
                                </span>

                            </div>


                            <div
                                class="flex items-center justify-between
                                       text-sm"
                            >

                                <span class="text-slate-500">
                                    Brutto
                                </span>

                                <span
                                    id="totalBrutto"
                                    class="font-semibold text-slate-800"
                                >
                                    Rp0
                                </span>

                            </div>


                            <div
                                class="flex items-center justify-between
                                       text-sm"
                            >

                                <span class="text-slate-500">
                                    Diskon
                                </span>

                                <span
                                    id="totalDiskon"
                                    class="font-semibold text-red-600"
                                >
                                    Rp0
                                </span>

                            </div>


                            <div
                                class="border-t border-slate-100 pt-4"
                            >

                                <div
                                    class="flex items-center justify-between"
                                >

                                    <span
                                        class="font-semibold text-slate-700"
                                    >
                                        Total Netto
                                    </span>

                                    <span
                                        id="totalNetto"
                                        class="text-xl font-bold text-blue-600"
                                    >
                                        Rp0
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Warning --}}

                        <div
                            class="mt-6 rounded-xl border border-orange-100
                                   bg-orange-50 p-4"
                        >

                            <p
                                class="text-xs leading-relaxed
                                       text-orange-700"
                            >

                                ⚠️ Stok akan bertambah setelah penerimaan
                                dikonfirmasi. Pastikan jumlah fisik barang
                                sudah sesuai.

                            </p>

                        </div>


                        {{-- Submit --}}

                        <button
                            type="submit"
                            id="btnSubmit"
                            class="mt-5 w-full rounded-xl bg-blue-600
                                   px-5 py-3 font-semibold text-white
                                   transition hover:bg-blue-700
                                   disabled:cursor-not-allowed
                                   disabled:bg-slate-300"
                        >
                            ✓ Konfirmasi Penerimaan
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const rows =
        document.querySelectorAll('[data-item-row]');

    const totalQtyElement =
        document.getElementById('totalQty');

    const totalBruttoElement =
        document.getElementById('totalBrutto');

    const totalDiskonElement =
        document.getElementById('totalDiskon');

    const totalNettoElement =
        document.getElementById('totalNetto');

    const btnSubmit =
        document.getElementById('btnSubmit');


    /*
    |--------------------------------------------------------------------------
    | Format Rupiah
    |--------------------------------------------------------------------------
    */

    function formatRupiah(number) {

        return new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }
        ).format(number);

    }


    /*
    |--------------------------------------------------------------------------
    | Hitung Semua Total
    |--------------------------------------------------------------------------
    */

    function hitungTotal() {

        let totalQty = 0;
        let totalBrutto = 0;
        let totalDiskon = 0;

        rows.forEach(function (row) {

            const qtyInput =
                row.querySelector('[data-qty]');

            const hargaInput =
                row.querySelector('[data-harga]');

            const diskonInput =
                row.querySelector('[data-diskon]');

            const subtotalElement =
                row.querySelector('[data-subtotal]');


            let qty =
                parseInt(qtyInput.value) || 0;

            let harga =
                parseFloat(hargaInput.value) || 0;

            let diskon =
                parseFloat(diskonInput.value) || 0;


            /*
            |--------------------------------------------------------------------------
            | Batasi Qty Berdasarkan Max
            |--------------------------------------------------------------------------
            */

            const maxQty =
                parseInt(qtyInput.max) || 0;

            if (qty > maxQty) {

                qty = maxQty;

                qtyInput.value =
                    maxQty;

            }

            if (qty < 0) {

                qty = 0;

                qtyInput.value = 0;

            }


            /*
            |--------------------------------------------------------------------------
            | Hitung
            |--------------------------------------------------------------------------
            */

            const brutto =
                qty * harga;

            const nettoItem =
                Math.max(
                    0,
                    brutto - diskon
                );


            subtotalElement.textContent =
                formatRupiah(nettoItem);


            totalQty += qty;

            totalBrutto += brutto;

            /*
            | Diskon hanya dihitung jika item diterima.
            */

            if (qty > 0) {

                totalDiskon +=
                    Math.min(
                        diskon,
                        brutto
                    );

            }

        });


        const totalNetto =
            Math.max(
                0,
                totalBrutto - totalDiskon
            );


        /*
        |--------------------------------------------------------------------------
        | Update Tampilan
        |--------------------------------------------------------------------------
        */

        totalQtyElement.textContent =
            totalQty + ' unit';

        totalBruttoElement.textContent =
            formatRupiah(totalBrutto);

        totalDiskonElement.textContent =
            formatRupiah(totalDiskon);

        totalNettoElement.textContent =
            formatRupiah(totalNetto);


        /*
        |--------------------------------------------------------------------------
        | Disable Submit Jika Tidak Ada Barang
        |--------------------------------------------------------------------------
        */

        btnSubmit.disabled =
            totalQty <= 0;

    }


    /*
    |--------------------------------------------------------------------------
    | Event Input
    |--------------------------------------------------------------------------
    */

    rows.forEach(function (row) {

        const inputs =
            row.querySelectorAll(
                '[data-qty], [data-harga], [data-diskon]'
            );

        inputs.forEach(function (input) {

            input.addEventListener(
                'input',
                hitungTotal
            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Hitung Saat Halaman Pertama Dibuka
    |--------------------------------------------------------------------------
    */

    hitungTotal();

});

</script>

@endsection