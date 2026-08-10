@extends('layouts.app')

@section('content')
<div class="p-4 space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Laporan Bulanan</h1>
            <p class="text-gray-500 text-sm">Laporan resmi rekap penjualan &amp; pembelian untuk owner.</p>
        </div>

        @can('create', App\Models\Laporan::class)
            <a href="{{ route('laporan-bulanan.create') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Buat Laporan</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="border rounded">
        <div class="px-4 py-3 border-b">
            <h2 class="font-bold">Daftar Laporan</h2>
            <p class="text-sm text-gray-500">{{ $laporan->count() }} laporan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">Kode</th>
                        <th class="px-4 py-2">Periode</th>
                        <th class="px-4 py-2 text-right">Total Penjualan</th>
                        <th class="px-4 py-2 text-right">Total Pembelian</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Dibuat Oleh</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        @php
                            $statusLabel = match($item->status) {
                                'draft' => 'Draft',
                                'terkirim' => 'Terkirim',
                                'ditinjau' => 'Ditinjau',
                            };
                            $statusColor = match($item->status) {
                                'draft' => 'text-gray-500',
                                'terkirim' => 'text-blue-600',
                                'ditinjau' => 'text-green-600',
                            };
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold">{{ $item->kode_laporan }}</td>
                            <td class="px-4 py-3">
                                {{ $item->periode_awal->format('d M Y') }} &mdash; {{ $item->periode_akhir->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-semibold {{ $statusColor }}">● {{ $statusLabel }}</td>
                            <td class="px-4 py-3">{{ $item->pembuat->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('laporan-bulanan.show', $item) }}" class="border rounded px-3 py-1 text-xs">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <p class="font-semibold">Belum Ada Laporan</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    @can('create', App\Models\Laporan::class)
                                        Buat laporan pertama untuk dikirim ke owner.
                                    @else
                                        Laporan akan muncul di sini setelah dikirim oleh admin.
                                    @endcan
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection