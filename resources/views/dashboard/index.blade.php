@extends('layouts.app')

@section('content')

<div class="space-y-7">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Dashboard
        </h1>

        <p class="text-slate-500 mt-1">
            Ringkasan aktivitas dan kondisi Toko Patuha Outdoor.
        </p>
    </div>


    {{-- STATISTIK MASTER DATA --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- TOTAL BARANG --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total Barang
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $totalBarang }}
                    </h2>

                    <p class="text-xs text-slate-400 mt-1">
                        produk terdaftar
                    </p>
                </div>

                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-xl">
                    📦
                </div>

            </div>

        </div>


        {{-- TOTAL VARIAN --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total Varian
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $totalVarian }}
                    </h2>

                    <p class="text-xs text-slate-400 mt-1">
                        varian produk
                    </p>
                </div>

                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-xl">
                    🎨
                </div>

            </div>

        </div>


        {{-- TOTAL SUPPLIER --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total Supplier
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $totalSupplier }}
                    </h2>

                    <p class="text-xs text-slate-400 mt-1">
                        supplier aktif
                    </p>
                </div>

                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-xl">
                    🚚
                </div>

            </div>

        </div>


        {{-- TOTAL STOK --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total Stok
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($totalStok, 0, ',', '.') }}
                    </h2>

                    <p class="text-xs text-slate-400 mt-1">
                        unit tersedia
                    </p>
                </div>

                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-xl">
                    📊
                </div>

            </div>

        </div>

    </div>


    {{-- RINGKASAN KEUANGAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- PENJUALAN --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Penjualan Bulan Ini
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-3">

                        Rp {{ number_format(
                            $totalPenjualan,
                            0,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <p class="text-sm text-slate-400 mt-2">
                        Total transaksi penjualan periode berjalan
                    </p>

                </div>

                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-xl">
                    💰
                </div>

            </div>

        </div>


        {{-- PEMBELIAN --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Pembelian Bulan Ini
                    </p>

                    <h2 class="text-3xl font-bold text-slate-900 mt-3">

                        Rp {{ number_format(
                            $totalPembelian,
                            0,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <p class="text-sm text-slate-400 mt-2">
                        Total pembelian barang periode berjalan
                    </p>

                </div>

                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-xl">
                    🛒
                </div>

            </div>

        </div>

    </div>


    {{-- STATUS OPERASIONAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- STOK MENIPIS --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Stok Menipis
                    </p>

                    <div class="flex items-end gap-2 mt-2">

                        <h2 class="text-3xl font-bold text-red-600">
                            {{ $stokMenipis->count() }}
                        </h2>

                        <span class="text-sm text-slate-500 mb-1">
                            varian
                        </span>

                    </div>

                    <p class="text-sm text-slate-400 mt-2">
                        Stok sudah mencapai batas minimum
                    </p>

                </div>

                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-xl">
                    ⚠️
                </div>

            </div>

        </div>


        {{-- MENUNGGU PENERIMAAN --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Menunggu Penerimaan
                    </p>

                    <div class="flex items-end gap-2 mt-2">

                        <h2 class="text-3xl font-bold text-blue-600">
                            {{ $menungguPenerimaan ?? 0 }}
                        </h2>

                        <span class="text-sm text-slate-500 mb-1">
                            perencanaan
                        </span>

                    </div>

                    <p class="text-sm text-slate-400 mt-2">
                        Perencanaan yang belum selesai diterima
                    </p>

                </div>

                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-xl">
                    📥
                </div>

            </div>

        </div>

    </div>


    {{-- CHART PENJUALAN --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-100">

            <h2 class="text-xl font-bold text-slate-900">
                📊 Penjualan per Channel
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Perbandingan nilai transaksi berdasarkan channel penjualan.
            </p>

        </div>

        <div class="p-6">

            @if($penjualanChannel->count() > 0)

                <div class="relative h-[320px] md:h-[380px]">

                    <canvas id="channelChart"></canvas>

                </div>

            @else

                <div class="py-16 text-center">

                    <div class="text-4xl mb-3">
                        📊
                    </div>

                    <h3 class="font-semibold text-slate-700">
                        Belum Ada Data Penjualan
                    </h3>

                    <p class="text-sm text-slate-400 mt-1">
                        Grafik akan muncul setelah terdapat transaksi.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- BOTTOM GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">


        {{-- TOP BARANG TERLARIS --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-slate-100">

                <h2 class="text-xl font-bold text-slate-900">
                    🔥 Top 5 Barang Terlaris
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Produk dengan jumlah penjualan tertinggi.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                Barang
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase">
                                Terjual
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($barangTerlaris as $item)

                            <tr class="border-t border-slate-100 hover:bg-slate-50">

                                <td class="px-6 py-4">

                                    <p class="font-semibold text-slate-800">
                                        {{ $item->nama_barang }}
                                    </p>

                                </td>

                                <td class="px-6 py-4 text-right">

                                    <span class="inline-flex px-3 py-1 rounded-lg bg-green-50 text-green-700 font-semibold">

                                        {{ $item->total_terjual }}
                                        unit

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="2" class="text-center py-12 text-slate-400">

                                    Belum ada data penjualan.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- STOK MENIPIS --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-slate-100 flex items-center justify-between">

                <div>

                    <h2 class="text-xl font-bold text-slate-900">
                        ⚠️ Stok Menipis
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Varian yang perlu dipertimbangkan untuk restock.
                    </p>

                </div>

                @if($stokMenipis->count() > 0)

                    <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-sm font-semibold">

                        {{ $stokMenipis->count() }}

                    </span>

                @endif

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                Barang
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                Varian
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-semibold text-slate-500 uppercase">
                                Stok
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-semibold text-slate-500 uppercase">
                                Min.
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($stokMenipis as $item)

                            <tr class="border-t border-slate-100 hover:bg-slate-50">

                                <td class="px-5 py-4">

                                    <p class="font-semibold text-slate-800">

                                        {{ $item->barang->nama_barang }}

                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">

                                        SKU:
                                        {{ $item->sku ?? '-' }}

                                    </p>

                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">

                                    {{ $item->warna ?: '-' }}

                                    /

                                    {{ $item->ukuran ?: '-' }}

                                </td>

                                <td class="px-5 py-4 text-center">

                                    <span class="inline-flex min-w-[40px] justify-center px-3 py-1 rounded-lg bg-red-50 text-red-600 font-bold">

                                        {{ $item->stok }}

                                    </span>

                                </td>

                                <td class="px-5 py-4 text-center text-slate-600">

                                    {{ $item->stok_minimum }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-12">

                                    <div class="text-3xl mb-2">
                                        ✅
                                    </div>

                                    <p class="font-semibold text-slate-700">
                                        Stok Aman
                                    </p>

                                    <p class="text-sm text-slate-400 mt-1">
                                        Tidak ada varian dengan stok menipis.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


{{-- CHART --}}
@if($penjualanChannel->count() > 0)

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const ctx =
            document.getElementById('channelChart');

        if (!ctx) {
            return;
        }

        new Chart(ctx, {

            type: 'bar',

            data: {

                labels: @json(
                    $penjualanChannel
                        ->pluck('channel')
                        ->map(fn ($channel) =>
                            ucfirst($channel)
                        )
                        ->values()
                ),

                datasets: [{

                    label:
                        'Total Penjualan',

                    data: @json(
                        $penjualanChannel
                            ->pluck('total')
                            ->values()
                    ),

                    borderRadius: 8,

                    borderSkipped: false

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return 'Rp ' +
                                    Number(
                                        context.raw
                                    ).toLocaleString(
                                        'id-ID'
                                    );

                            }

                        }

                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function(value) {

                                return 'Rp ' +
                                    Number(value)
                                        .toLocaleString(
                                            'id-ID'
                                        );

                            }

                        }

                    }

                }

            }

        });

    });

</script>

@endif

@endsection