@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold mb-8">
        Dashboard
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">Total Barang</p>
            <h2 class="text-3xl font-bold mt-2">
                {{ $totalBarang }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">Total Varian</p>
            <h2 class="text-3xl font-bold mt-2">
                {{ $totalVarian }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">Total Supplier</p>
            <h2 class="text-3xl font-bold mt-2">
                {{ $totalSupplier }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">Total Stok</p>
            <h2 class="text-3xl font-bold mt-2">
                {{ $totalStok }}
            </h2>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">

        <div class="bg-green-500 text-white p-4 rounded-2xl shadow">

            <h2 class="text-lg">
                Total Penjualan
            </h2>

            <p class="text-3xl font-bold mt-3">
                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
            </p>

        </div>

        <div class="bg-blue-500 text-white p-4 rounded-2xl shadow">

            <h2 class="text-lg">
                Total Pembelian
            </h2>

            <p class="text-3xl font-bold mt-3">
                Rp {{ number_format($totalPembelian, 0, ',', '.') }}
            </p>

        </div>

    </div>

    {{-- CHART PENJUALAN --}}
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-8">

        <h2 class="text-xl font-bold mb-5">

            📊 Penjualan per Channel

        </h2>

        <div class="w-full overflow-hidden">
            <div class="relative h-[300px] md:h-[400px]">
                <canvas id="channelChart"></canvas>
            </div>
        </div>

    </div>

    {{-- TOP BARANG TERLARIS --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">

        <div class="p-5 border-b">

            <h2 class="text-xl font-bold">

                🔥 Top 5 Barang Terlaris

            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Barang
                    </th>

                    <th class="px-6 py-4 text-center">
                        Total Terjual
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($barangTerlaris as $item)

                    <tr class="border-t">

                        <td class="px-6 py-4">
                            {{ $item->nama_barang }}
                        </td>

                        <td class="px-6 py-4 text-center font-bold text-green-600">
                            {{ $item->total_terjual }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="2" class="text-center py-8">

                            Belum ada data penjualan

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- STOK MENIPIS --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="p-5 border-b">

            <h2 class="text-xl font-bold text-red-600">

                ⚠️ Stok Menipis

            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Barang
                    </th>

                    <th class="px-6 py-4 text-left">
                        Warna
                    </th>

                    <th class="px-6 py-4 text-left">
                        Ukuran
                    </th>

                    <th class="px-6 py-4 text-left">
                        Stok
                    </th>

                    <th class="px-6 py-4 text-left">
                        Minimum
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($stokMenipis as $item)

                    <tr class="border-t">

                        <td class="px-6 py-4">
                            {{ $item->barang->nama_barang }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->warna }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->ukuran }}
                        </td>

                        <td class="px-6 py-4 text-red-600 font-bold">
                            {{ $item->stok }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->stok_minimum }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center py-8">

                            Tidak ada stok menipis

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <script>

        const ctx = document.getElementById('channelChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($penjualanChannel as $item)
                        "{{ ucfirst($item->channel) }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Penjualan',
                    data: [
                        @foreach($penjualanChannel as $item)
                            {{ $item->total }},
                        @endforeach
                    ],
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    </script>

@endsection