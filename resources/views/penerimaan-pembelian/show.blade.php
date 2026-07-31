@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <a href="{{ route('penerimaan-pembelian.index') }}" class="text-sm text-blue-600">← Kembali ke Penerimaan</a>
            <h1 class="text-2xl font-bold">Detail Penerimaan Barang</h1>
            <p class="text-gray-500 text-sm">Informasi transaksi barang yang telah diterima dari supplier.</p>
        </div>
        <span class="text-sm font-semibold text-green-600">✓ Diterima</span>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- INFO UTAMA --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        <div class="border rounded p-4 xl:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold">Informasi Penerimaan</h2>
                <span class="bg-blue-50 text-blue-700 rounded px-3 py-1 text-sm font-bold">{{ $pembelian->no_faktur }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase">No. Faktur / Surat Jalan</p>
                    <p class="font-semibold mt-1">{{ $pembelian->no_faktur }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Tanggal Penerimaan</p>
                    <p class="font-semibold mt-1">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Supplier</p>
                    <p class="font-semibold mt-1">{{ $pembelian->supplier->nama_supplier ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Diterima Oleh</p>
                    <p class="font-semibold mt-1">{{ $pembelian->user->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="border rounded p-4">
            <h2 class="font-bold mb-2">📋 Perencanaan Pembelian</h2>

            @if($pembelian->perencanaan)
                <p class="text-sm text-gray-500 mb-2">Penerimaan ini berasal dari:</p>
                <div class="bg-purple-50 border border-purple-200 rounded p-3 text-sm">
                    <p class="text-xs text-purple-500 uppercase">No. Perencanaan</p>
                    <p class="font-bold text-purple-800 mt-1">{{ $pembelian->perencanaan->no_perencanaan }}</p>
                    <p class="text-xs text-purple-600 mt-1">{{ \Carbon\Carbon::parse($pembelian->perencanaan->tanggal_perencanaan)->format('d M Y') }}</p>
                </div>
                <a href="{{ route('perencanaan-pembelian.show', $pembelian->perencanaan) }}" class="block text-center mt-3 border rounded px-4 py-2 text-sm font-semibold">Lihat Perencanaan</a>
            @else
                <p class="text-sm text-gray-500">Tidak memiliki referensi perencanaan.</p>
            @endif
        </div>
    </div>

    {{-- DETAIL BARANG --}}
    <div class="border rounded">
        <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-bold">Barang yang Diterima</h2>
                <p class="text-sm text-gray-500">Rincian barang yang masuk pada penerimaan ini.</p>
            </div>
            <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $pembelian->detailPembelian->count() }} Varian</span>
        </div>

        @if($pembelian->detailPembelian->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">Barang</th>
                            <th class="px-4 py-2">SKU</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Harga Satuan</th>
                            <th class="px-4 py-2 text-right">Brutto</th>
                            <th class="px-4 py-2 text-right">Diskon</th>
                            <th class="px-4 py-2 text-right">Netto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->detailPembelian as $detail)
                            @php
                                $brutto = $detail->qty * $detail->harga_satuan;
                                $netto = max(0, $brutto - $detail->diskon);
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $detail->varian->barang->nama_barang ?? 'Barang' }}</p>
                                    <p class="text-sm text-gray-500">{{ $detail->varian->warna ?? '-' }}{{ $detail->varian->ukuran ? ' / ' . $detail->varian->ukuran : '' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $detail->varian->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-green-700 font-bold">+{{ $detail->qty }}</td>
                                <td class="px-4 py-3 text-right">Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-medium">Rp{{ number_format($brutto, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-red-600">{{ $detail->diskon > 0 ? '- Rp' . number_format($detail->diskon, 0, ',', '.') : 'Rp0' }}</td>
                                <td class="px-4 py-3 text-right font-bold">Rp{{ number_format($netto, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-10 text-center text-gray-400">Tidak ada detail barang pada penerimaan ini.</div>
        @endif
    </div>

    {{-- RINGKASAN --}}
    <div class="flex justify-end">
        <div class="w-full md:w-[380px] border rounded p-4">
            <h2 class="font-bold mb-3">Ringkasan Pembelian</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Brutto</span>
                    <span class="font-semibold">Rp{{ number_format($pembelian->total_brutto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Diskon</span>
                    <span class="font-semibold text-red-600">- Rp{{ number_format($pembelian->total_diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="font-bold">Total Netto</span>
                    <span class="text-xl font-bold text-blue-600">Rp{{ number_format($pembelian->total_netto, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO STOK --}}
    <div class="border border-green-200 bg-green-50 rounded p-4 text-sm text-green-800">
        <p class="font-semibold mb-1">✓ Penerimaan Telah Diproses</p>
        <p>Barang pada transaksi ini telah ditambahkan ke stok sistem dan menjadi bagian dari riwayat pembelian aktual.</p>
    </div>

</div>
@endsection