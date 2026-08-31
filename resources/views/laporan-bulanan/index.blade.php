@extends('layouts.app')

@section('content')
<div class="p-4 space-y-4">

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Laporan Bulanan</h1>
            <p class="text-gray-500 text-sm">Pilih periode untuk membuat rekap penjualan, pembelian, dan stok langsung dari data sistem.</p>
        </div>

        @can('create', App\Models\Laporan::class)
            <a href="{{ route('laporan-bulanan.create') }}" class="w-full md:w-auto bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold text-center">+ Buat Rekap</a>
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

        <div class="hidden md:block overflow-x-auto">
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
                                'draft' => 'Tersedia',
                                'terkirim' => 'Tersedia',
                                'ditinjau' => 'Ditinjau',
                            };
                            $statusColor = match($item->status) {
                                'draft' => 'text-green-600',
                                'terkirim' => 'text-green-600',
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
                                    Buat rekap untuk periode yang ingin dilihat.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporan->isNotEmpty())
            <div class="md:hidden divide-y">
                @foreach($laporan as $item)
                    @php
                        $statusLabel = match($item->status) {
                            'draft' => 'Tersedia',
                            'terkirim' => 'Tersedia',
                            'ditinjau' => 'Ditinjau',
                        };
                        $statusColor = match($item->status) {
                            'draft' => 'text-green-600',
                            'terkirim' => 'text-green-600',
                            'ditinjau' => 'text-green-600',
                        };
                    @endphp

                    <article class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold break-words">{{ $item->kode_laporan }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $item->periode_awal->format('d M Y') }} &mdash; {{ $item->periode_akhir->format('d M Y') }}
                                </p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold {{ $statusColor }}">● {{ $statusLabel }}</span>
                        </div>

                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div class="min-w-0">
                                <dt class="text-xs text-gray-500">Total Penjualan</dt>
                                <dd class="mt-0.5 font-semibold break-words">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs text-gray-500">Total Pembelian</dt>
                                <dd class="mt-0.5 font-semibold break-words">Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}</dd>
                            </div>
                            <div class="col-span-2 min-w-0">
                                <dt class="text-xs text-gray-500">Dibuat Oleh</dt>
                                <dd class="mt-0.5 break-words">{{ $item->pembuat->name ?? '-' }}</dd>
                            </div>
                        </dl>

                        <a href="{{ route('laporan-bulanan.show', $item) }}" class="inline-block border rounded px-3 py-1.5 text-xs">Detail</a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="md:hidden text-center py-10 px-4">
                <p class="font-semibold">Belum Ada Laporan</p>
                <p class="text-sm text-gray-500 mt-1">
                    Buat rekap untuk periode yang ingin dilihat.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection
