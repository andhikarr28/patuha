@extends('layouts.app')

@section('content')
    <div class="p-4 space-y-6">
        <a href="{{ route('penjualan.struk', $penjualan->id) }}" target="_blank"
            class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">
            🖨 Cetak Struk
        </a>

        <div class="border rounded p-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-3">
                <div class="text-sm">
                    <h1 class="text-2xl font-bold mb-2">Detail Transaksi Penjualan</h1>
                    <p class="mb-1"><span class="font-semibold">No Nota:</span> {{ $penjualan->no_nota }}</p>
                    <p class="mb-1"><span class="font-semibold">Tanggal:</span> {{ $penjualan->tanggal_penjualan }}</p>
                    <p class="mb-1"><span class="font-semibold">Channel:</span> {{ ucfirst($penjualan->channel) }}</p>
                    <p><span class="font-semibold">Metode Pembayaran:</span> {{ ucfirst($penjualan->metode_pembayaran) }}
                    </p>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-gray-500 text-sm mb-1">Total Transaksi</p>
                    <h2 class="text-2xl font-bold text-green-600">Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="border rounded">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">Barang</th>
                        <th class="px-4 py-2">Varian</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detail as $item)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $item->varian->barang->nama_barang }}</td>
                            <td class="px-4 py-3">{{ $item->varian->warna }} / {{ $item->varian->ukuran }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">Belum ada item transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection