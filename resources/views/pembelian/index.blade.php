@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Riwayat Pembelian</h1>
            <p class="text-gray-500 text-sm">Daftar pembelian yang telah diproses melalui penerimaan barang.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('perencanaan-pembelian.create') }}" class="border rounded px-4 py-2 text-sm font-semibold">📋 Buat Perencanaan</a>
            <a href="{{ route('penerimaan-pembelian.index') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">📥 Penerimaan Barang</a>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- TABLE --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <div>
                <h2 class="font-bold">Transaksi Pembelian</h2>
                <p class="text-sm text-gray-500">Riwayat barang yang telah diterima dari supplier.</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $pembelian->count() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">No. Faktur</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Supplier</th>
                        <th class="px-4 py-2 text-right">Total Netto</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelian as $item)
                        <tr class="border-b">
                            <td class="px-4 py-3">
                                <p class="font-semibold">{{ $item->no_faktur }}</p>
                                <p class="text-xs text-gray-400">ID #{{ $item->id }}</p>
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}</td>
                            <td class="px-4 py-3">🚚 {{ $item->supplier->nama_supplier ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($item->total_netto, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('detail-pembelian.index', ['pembelian_id' => $item->id]) }}" class="border rounded px-3 py-1 text-xs">👁 Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <p class="font-semibold">Belum Ada Riwayat Pembelian</p>
                                <p class="text-sm text-gray-500 mt-1">Pembelian akan muncul setelah proses penerimaan barang dilakukan.</p>
                                <a href="{{ route('perencanaan-pembelian.create') }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Buat Perencanaan</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection