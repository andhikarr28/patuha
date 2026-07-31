@extends('layouts.app')

@section('content')
    @php
        $status = strtolower($perencanaan->status ?? 'draft');
        $statusColor = match ($status) {
            'draft' => 'text-amber-700',
            'menunggu' => 'text-blue-700',
            'sebagian' => 'text-purple-700',
            'selesai' => 'text-emerald-700',
            'dibatalkan' => 'text-red-700',
            default => 'text-gray-700',
        };
        $totalRencana = $perencanaan->details->sum('qty_rencana');
        $totalDiterima = $perencanaan->details->sum('qty_diterima');
        $estimasiTotal = $perencanaan->details->sum(fn($d) => $d->qty_rencana * $d->estimasi_harga);
        $progress = $totalRencana > 0 ? min(100, round(($totalDiterima / $totalRencana) * 100)) : 0;
    @endphp

    <div class="p-4 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold">Detail Perencanaan</h1>
                    <span class="text-sm font-semibold {{ $statusColor }}">● {{ ucfirst($status) }}</span>
                </div>
                <p class="text-gray-500 text-sm mt-1">Informasi lengkap perencanaan pembelian dan progres penerimaan barang.
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('perencanaan-pembelian.struk', $perencanaan->id) }}" target="_blank"
                    class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">
                    🖨 Cetak Surat Pesanan
                </a>
                <a href="{{ route('perencanaan-pembelian.index') }}"
                    class="border rounded px-4 py-2 text-sm font-semibold">← Kembali</a>
                    
                @if($status !== 'selesai' && $status !== 'dibatalkan')
                    <a href="{{ route('penerimaan-pembelian.create', $perencanaan->id) }}"
                        class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">📥 Terima Barang</a>
                @endif
            </div>
        </div>

        {{-- INFO UTAMA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">No. Perencanaan</p>
                <p class="font-bold mt-1">{{ $perencanaan->no_perencanaan }}</p>
            </div>
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Tanggal</p>
                <p class="font-bold mt-1">{{ \Carbon\Carbon::parse($perencanaan->tanggal_perencanaan)->format('d M Y') }}
                </p>
            </div>
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Supplier</p>
                <p class="font-bold mt-1">{{ $perencanaan->supplier->nama_supplier ?? '-' }}</p>
            </div>
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Dibuat Oleh</p>
                <p class="font-bold mt-1">{{ $perencanaan->user->name ?? '-' }}</p>
            </div>
        </div>

        {{-- PROGRESS --}}
        <div class="border rounded p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div>
                    <h2 class="font-bold">Progress Penerimaan</h2>
                    <p class="text-sm text-gray-500">Perbandingan jumlah barang direncanakan vs sudah diterima.</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xl font-bold text-blue-600">{{ $totalDiterima }} / {{ $totalRencana }}</p>
                    <p class="text-sm text-gray-500">unit diterima</p>
                </div>
            </div>

            <div class="mt-3 h-2.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
            <div class="mt-2 flex justify-between text-sm text-gray-500">
                <span>{{ $progress }}% diterima</span>
                <span>Sisa: {{ max(0, $totalRencana - $totalDiterima) }} unit</span>
            </div>
        </div>

        {{-- DETAIL BARANG --}}
        <div class="border rounded">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div>
                    <h2 class="font-bold">Daftar Barang</h2>
                    <p class="text-sm text-gray-500">Barang dalam perencanaan pembelian ini.</p>
                </div>
                <span class="bg-gray-100 text-sm rounded px-3 py-1">{{ $perencanaan->details->count() }} Varian</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2">SKU</th>
                            <th class="px-4 py-2 text-center">Rencana</th>
                            <th class="px-4 py-2 text-center">Diterima</th>
                            <th class="px-4 py-2 text-center">Sisa</th>
                            <th class="px-4 py-2 text-right">Estimasi Harga</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perencanaan->details as $detail)
                            @php
                                $qtyRencana = (int) $detail->qty_rencana;
                                $qtyDiterima = (int) $detail->qty_diterima;
                                $sisa = max(0, $qtyRencana - $qtyDiterima);
                                $subtotal = $qtyRencana * $detail->estimasi_harga;
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $detail->varian->barang->nama_barang ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ $detail->varian->warna ?? '-' }} /
                                        {{ $detail->varian->ukuran ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $detail->varian->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $qtyRencana }}</td>
                                <td class="px-4 py-3 text-center text-emerald-700 font-semibold">{{ $qtyDiterima }}</td>
                                <td
                                    class="px-4 py-3 text-center {{ $sisa > 0 ? 'text-amber-700 font-semibold' : 'text-emerald-700' }}">
                                    {{ $sisa > 0 ? $sisa : 'Selesai' }}
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($detail->estimasi_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-400">Tidak ada barang</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($perencanaan->details->isNotEmpty())
                        <tfoot class="border-t bg-gray-50">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-600">Estimasi Total</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-blue-600">Rp
                                    {{ number_format($estimasiTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- CATATAN --}}
        @if($perencanaan->catatan)
            <div class="border rounded p-4">
                <h2 class="font-bold mb-2">📝 Catatan</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $perencanaan->catatan }}</p>
            </div>
        @endif

        {{-- INFORMASI ALUR --}}
        @if($status !== 'selesai' && $status !== 'dibatalkan')
            <div class="border border-blue-200 bg-blue-50 rounded p-4 text-sm text-blue-800">
                <p class="font-semibold mb-1">Tahap Selanjutnya</p>
                <p>Perencanaan ini belum selesai. Gunakan menu <strong>Terima Barang</strong> ketika barang dari supplier tiba.
                    Stok hanya bertambah setelah penerimaan dikonfirmasi.</p>
            </div>
        @endif

    </div>
@endsection