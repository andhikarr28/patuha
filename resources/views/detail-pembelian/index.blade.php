@extends('layouts.app')

@section('content')
    <div class="p-4 space-y-6">

        <h1 class="text-2xl font-bold">Detail Pembelian</h1>

        @if(session('success'))
            <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('pembelian.struk', $pembelian->id) }}" target="_blank"
                class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">
                🖨 Cetak Surat Penerimaan
            </a>
        </div>

        {{-- INFORMASI PEMBELIAN --}}
        <div class="border rounded p-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-3">
                <div class="text-sm">
                    <h2 class="font-bold text-lg mb-2">Informasi Pembelian</h2>
                    <p class="mb-1"><span class="font-semibold">No Faktur:</span> {{ $pembelian->no_faktur }}</p>
                    <p class="mb-1"><span class="font-semibold">Tanggal:</span>
                        {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}</p>
                    <p><span class="font-semibold">Supplier:</span> {{ $pembelian->supplier->nama_supplier }}</p>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-gray-500 text-sm mb-1">Total Netto</p>
                    <h2 class="text-2xl font-bold text-green-600">Rp
                        {{ number_format($pembelian->total_netto, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        {{-- DETAIL BARANG --}}
        <div class="border rounded">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-2">Barang</th>
                        <th class="px-4 py-2">Varian</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Diskon</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detail as $item)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $item->varian->barang->nama_barang }}</td>
                            <td class="px-4 py-3">{{ $item->varian->warna }} / {{ $item->varian->ukuran }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-red-600">Rp {{ number_format($item->diskon, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-400">Belum ada data detail pembelian</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- RINGKASAN --}}
        <div class="flex justify-end">
            <div class="w-full md:w-[380px] border rounded p-4">
                <div class="flex justify-between text-sm mb-2">
                    <span>Total Brutto</span>
                    <span>Rp {{ number_format($pembelian->total_brutto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-2 text-red-600">
                    <span>Total Diskon</span>
                    <span>Rp {{ number_format($pembelian->total_diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-3 text-lg font-bold text-green-600">
                    <span>Total Netto</span>
                    <span>Rp {{ number_format($pembelian->total_netto, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
@endsection