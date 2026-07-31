@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Perencanaan Pembelian</h1>
            <p class="text-gray-500 text-sm">Buat dan kelola rencana pembelian barang dari supplier.</p>
        </div>
        <a href="{{ route('perencanaan-pembelian.create') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Buat Perencanaan</a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- SUMMARY --}}
    @php
        $jumlahDraft = $perencanaan->where('status', 'draft')->count();
        $jumlahDipesan = $perencanaan->where('status', 'dipesan')->count();
        $jumlahSebagian = $perencanaan->where('status', 'sebagian_diterima')->count();
        $jumlahSelesai = $perencanaan->where('status', 'selesai')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Draft</p>
            <p class="text-xl font-bold">{{ $jumlahDraft }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Menunggu Penerimaan</p>
            <p class="text-xl font-bold text-blue-600">{{ $jumlahDipesan }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Diterima Sebagian</p>
            <p class="text-xl font-bold text-orange-600">{{ $jumlahSebagian }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Selesai</p>
            <p class="text-xl font-bold text-green-600">{{ $jumlahSelesai }}</p>
        </div>
    </div>

    {{-- INFO --}}
    <div class="border border-blue-200 bg-blue-50 rounded p-4 text-sm text-blue-800">
        Buat perencanaan barang yang akan dibeli dari supplier. Perencanaan tidak langsung menambah stok — stok baru bertambah saat barang benar-benar diterima.
    </div>

    {{-- LIST --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <div>
                <h2 class="font-bold">Daftar Perencanaan</h2>
                <p class="text-sm text-gray-500">Riwayat dan status seluruh perencanaan pembelian.</p>
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
                            <th class="px-4 py-2">Estimasi</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perencanaan as $item)
                            @php
                                $totalQty = $item->details->sum('qty_rencana');
                                $totalEstimasi = $item->details->sum(fn($d) => $d->qty_rencana * $d->estimasi_harga);
                                $statusLabel = match($item->status) {
                                    'draft' => 'Draft',
                                    'dipesan' => 'Dipesan',
                                    'sebagian_diterima' => 'Sebagian Diterima',
                                    'selesai' => 'Selesai',
                                    'dibatalkan' => 'Dibatalkan',
                                    default => ucfirst(str_replace('_', ' ', $item->status)),
                                };
                                $statusColor = match($item->status) {
                                    'dipesan' => 'text-blue-600',
                                    'sebagian_diterima' => 'text-orange-600',
                                    'selesai' => 'text-green-600',
                                    'dibatalkan' => 'text-red-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $item->no_perencanaan }}</p>
                                    <p class="text-xs text-gray-400">Dibuat oleh: {{ $item->user->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_perencanaan)->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $item->supplier->nama_supplier ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $item->details->count() }} Varian</p>
                                    <p class="text-xs text-gray-500">{{ $totalQty }} unit direncanakan</p>
                                </td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold {{ $statusColor }}">{{ $statusLabel }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2 flex-wrap">
                                        <a href="{{ route('perencanaan-pembelian.show', $item) }}" class="border rounded px-3 py-1 text-xs">Detail</a>

                                        @if(in_array($item->status, ['dipesan', 'sebagian_diterima']))
                                            <a href="{{ route('penerimaan-pembelian.create', $item) }}" class="bg-blue-600 text-white rounded px-3 py-1 text-xs">📦 Terima</a>
                                        @endif

                                        @if(in_array($item->status, ['draft', 'dipesan']))
                                            <form action="{{ route('perencanaan-pembelian.cancel', $item) }}" method="POST" onsubmit="return confirm('Batalkan perencanaan ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-red-50 text-red-600 rounded px-3 py-1 text-xs">Batalkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Belum Ada Perencanaan Pembelian</p>
                <p class="text-sm text-gray-500 mt-1">Buat perencanaan untuk menentukan barang dan jumlah yang akan dipesan dari supplier.</p>
                <a href="{{ route('perencanaan-pembelian.create') }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Buat Perencanaan Pertama</a>
            </div>
        @endif
    </div>

</div>
@endsection