@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Penerimaan Barang</h1>
            <p class="text-gray-500 text-sm">Proses penerimaan barang berdasarkan perencanaan pembelian.</p>
        </div>
        <a href="{{ route('perencanaan-pembelian.index') }}" class="bg-slate-800 text-white rounded px-4 py-2 text-sm font-semibold">📋 Lihat Perencanaan</a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- INFO --}}
    <div class="border border-blue-200 bg-blue-50 rounded p-4 text-sm text-blue-800">
        Pilih salah satu perencanaan di bawah untuk mencatat barang yang telah diterima dari supplier. Stok baru bertambah setelah penerimaan dikonfirmasi.
    </div>

    {{-- LIST --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <div>
                <h2 class="font-bold">Menunggu Penerimaan</h2>
                <p class="text-sm text-gray-500">Perencanaan yang masih memiliki barang belum diterima seluruhnya.</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $perencanaan->count() }} Perencanaan</span>
        </div>

        @if($perencanaan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">No. Perencanaan</th>
                            <th class="px-4 py-2">Tanggal</th>
                            <th class="px-4 py-2">Supplier</th>
                            <th class="px-4 py-2">Barang</th>
                            <th class="px-4 py-2">Progress</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perencanaan as $item)
                            @php
                                $totalRencana = $item->details->sum('qty_rencana');
                                $totalDiterima = $item->details->sum('qty_diterima');
                                $totalSisa = max(0, $totalRencana - $totalDiterima);
                                $progress = $totalRencana > 0 ? round(($totalDiterima / $totalRencana) * 100) : 0;
                                $statusLabel = match($item->status) {
                                    'sebagian_diterima' => 'Sebagian Diterima',
                                    'dipesan' => 'Dipesan',
                                    default => ucfirst(str_replace('_', ' ', $item->status)),
                                };
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3 font-semibold">{{ $item->no_perencanaan }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_perencanaan)->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $item->supplier->nama_supplier ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $item->details->count() }} Varian</p>
                                    <p class="text-xs text-gray-500">Total rencana: {{ $totalRencana }} unit</p>
                                </td>
                                <td class="px-4 py-3 min-w-[180px]">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-500">{{ $totalDiterima }}/{{ $totalRencana }} diterima</span>
                                        <span class="font-semibold">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-gray-200 overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ min(100, $progress) }}%"></div>
                                    </div>
                                    @if($totalSisa > 0)
                                        <p class="text-xs text-orange-600 mt-1">Sisa {{ $totalSisa }} unit</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold {{ $item->status === 'sebagian_diterima' ? 'text-orange-600' : ($item->status === 'dipesan' ? 'text-blue-600' : 'text-gray-600') }}">
                                    {{ $statusLabel }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('perencanaan-pembelian.show', $item) }}" class="border rounded px-3 py-1 text-xs">Detail</a>
                                        <a href="{{ route('penerimaan-pembelian.create', $item) }}" class="bg-blue-600 text-white rounded px-3 py-1 text-xs">📦 Terima Barang</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Tidak Ada Penerimaan Tertunda</p>
                <p class="text-sm text-gray-500 mt-1">Belum ada perencanaan pembelian yang menunggu proses penerimaan barang.</p>
                <a href="{{ route('perencanaan-pembelian.index') }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Lihat Perencanaan Pembelian</a>
            </div>
        @endif
    </div>

</div>
@endsection