@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Penjualan</h1>
            <p class="text-gray-500 text-sm">Kelola transaksi penjualan toko dan marketplace.</p>
        </div>
        <a href="{{ route('penjualan.create') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Transaksi Baru</a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- TABLE --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <div>
                <h2 class="font-bold">Riwayat Transaksi</h2>
                <p class="text-sm text-gray-500">Daftar seluruh transaksi penjualan yang tercatat.</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $penjualan->count() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">No. Nota</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Channel</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $item)
                        <tr class="border-b">
                            <td class="px-4 py-3">
                                <p class="font-semibold">{{ $item->no_nota }}</p>
                                <p class="text-xs text-gray-400">ID #{{ $item->id }}</p>
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                {{ strtolower($item->channel) === 'shopee' ? '🛍️ Shopee' : '🏪 ' . ucfirst($item->channel) }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('detail-penjualan.index', ['penjualan_id' => $item->id]) }}" class="border rounded px-3 py-1 text-xs">👁 Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <p class="font-semibold">Belum Ada Transaksi</p>
                                <p class="text-sm text-gray-500 mt-1">Transaksi penjualan akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection