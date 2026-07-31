@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Laporan Penjualan</h1>
            <p class="text-gray-500 text-sm">Rekap transaksi penjualan berdasarkan periode yang dipilih.</p>
        </div>

        <a href="{{ route('laporan.penjualan.pdf', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir]) }}" target="_blank" class="bg-red-600 text-white rounded px-4 py-2 text-sm font-semibold text-center">
            📄 Cetak PDF
        </a>
    </div>

    {{-- FILTER --}}
    <div class="border rounded p-4">
        <h2 class="font-bold mb-1">Filter Periode</h2>
        <p class="text-sm text-gray-500 mb-3">Pilih rentang tanggal transaksi yang ingin ditampilkan.</p>

        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-semibold mb-1">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">🔍 Tampilkan</button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('laporan.penjualan') }}" class="w-full text-center bg-gray-100 text-gray-700 rounded px-4 py-2 text-sm font-semibold">Reset Bulan Ini</a>
                </div>
            </div>
        </form>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <p class="text-xl font-bold">{{ number_format($jumlahTransaksi) }}</p>
            <p class="text-xs text-gray-400">transaksi</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Penjualan</p>
            <p class="text-xl font-bold text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400">periode terpilih</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Rata-rata Transaksi</p>
            <p class="text-xl font-bold">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400">per transaksi</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-bold">Data Penjualan</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }} — {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $jumlahTransaksi }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nota</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Channel</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $item)
                        @php
                            $channel = strtolower($item->channel);
                            $channelLabel = match($channel) {
                                'shopee' => 'Shopee',
                                'tokopedia' => 'Tokopedia',
                                'tiktok' => 'TikTok Shop',
                                default => 'Offline / POS',
                            };
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $item->no_nota }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $channelLabel }}</td>
                            <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('detail-penjualan.index', ['penjualan_id' => $item->id]) }}" class="bg-gray-100 rounded px-3 py-1 text-xs font-semibold">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <p class="font-semibold">Belum Ada Penjualan</p>
                                <p class="text-sm text-gray-500 mt-1">Tidak ada transaksi penjualan pada periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penjualan->isNotEmpty())
            <div class="bg-slate-900 text-white px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <div>
                    <p class="text-sm text-slate-300">Total Penjualan Periode Ini</p>
                    <p class="text-xs text-slate-400">{{ $jumlahTransaksi }} transaksi tercatat</p>
                </div>
                <p class="text-xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>
        @endif
    </div>

</div>
@endsection