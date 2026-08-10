@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-gray-500">Ringkasan aktivitas Toko Patuha Outdoor</p>
    </div>

    {{-- RINGKASAN ANGKA --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Barang</p>
            <p class="text-xl font-bold">{{ $totalBarang }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Varian</p>
            <p class="text-xl font-bold">{{ $totalVarian }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Supplier</p>
            <p class="text-xl font-bold">{{ $totalSupplier }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Stok</p>
            <p class="text-xl font-bold">{{ number_format($totalStok, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- KEUANGAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Penjualan Bulan Ini</p>
            <p class="text-xl font-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Pembelian Bulan Ini</p>
            <p class="text-xl font-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- STATUS OPERASIONAL --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Stok Menipis</p>
            <p class="text-xl font-bold text-red-600">{{ $stokMenipis->count() }} varian</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Menunggu Penerimaan</p>
            <p class="text-xl font-bold text-blue-600">{{ $menungguPenerimaan ?? 0 }} perencanaan</p>
        </div>
    </div>

    {{-- CHART PENJUALAN --}}
    <div class="border rounded p-4">
        <h2 class="font-bold mb-3">Penjualan per Channel</h2>

        @if($penjualanChannel->count() > 0)
            <canvas id="channelChart" height="100"></canvas>
        @else
            <p class="text-gray-400 text-sm">Belum ada data penjualan.</p>
        @endif
        
    </div>

    {{-- TOP BARANG TERLARIS --}}
    <div class="border rounded p-4">
        <h2 class="font-bold mb-3">Top 5 Barang Terlaris</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2">Barang</th>
                    <th class="py-2 text-right">Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangTerlaris as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->nama_barang }}</td>
                        <td class="py-2 text-right">{{ $item->total_terjual }} unit</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Belum ada data penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- STOK MENIPIS --}}
    <div class="border rounded p-4">
        <h2 class="font-bold mb-3">Stok Menipis</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2">Barang</th>
                    <th class="py-2">Varian</th>
                    <th class="py-2 text-center">Stok</th>
                    <th class="py-2 text-center">Min.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokMenipis as $item)
                    <tr class="border-b">
                        <td class="py-2">
                            {{ $item->barang->nama_barang }}
                            <div class="text-xs text-gray-400">SKU: {{ $item->sku ?? '-' }}</div>
                        </td>
                        <td class="py-2">{{ $item->warna ?: '-' }} / {{ $item->ukuran ?: '-' }}</td>
                        <td class="py-2 text-center text-red-600 font-semibold">{{ $item->stok }}</td>
                        <td class="py-2 text-center">{{ $item->stok_minimum }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-400">Stok aman, tidak ada varian menipis.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@if($penjualanChannel->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('channelChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($penjualanChannel->pluck('channel')->map(fn($c) => ucfirst($c))->values()),
            datasets: [{
                label: 'Total Penjualan',
                data: @json($penjualanChannel->pluck('total')->values())
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection