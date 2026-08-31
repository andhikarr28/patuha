@extends('layouts.app')

@section('content')
    @php
        $jenisLaporan = $jenisLaporan ?? 'lengkap';
        $statusLabel = match ($laporan->status) {
            'draft' => 'Tersedia',
            'terkirim' => 'Tersedia',
            'ditinjau' => 'Sudah Ditinjau',
        };
        $statusColor = match ($laporan->status) {
            'draft' => 'text-green-600',
            'terkirim' => 'text-green-600',
            'ditinjau' => 'text-green-600',
        };
        $jenisLabel = match ($jenisLaporan) {
            'penjualan' => 'Rekap Penjualan',
            'pembelian' => 'Rekap Pembelian',
            default => 'Rekap Lengkap',
        };
        $menampilkanPenjualan = $jenisLaporan !== 'pembelian';
        $menampilkanPembelian = $jenisLaporan !== 'penjualan';
        $laporanLengkap = $jenisLaporan === 'lengkap';
    @endphp

    <div class="p-4 space-y-4">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('laporan-bulanan.index') }}" class="text-sm text-blue-600">← Kembali ke Daftar Laporan</a>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <h1 class="text-2xl font-bold break-words">{{ $laporan->kode_laporan }}</h1>
                    <span class="text-sm font-semibold {{ $statusColor }}">● {{ $statusLabel }}</span>
                </div>
                <p class="text-gray-500 text-sm">
                    Periode {{ $laporan->periode_awal->format('d M Y') }} &mdash;
                    {{ $laporan->periode_akhir->format('d M Y') }}
                </p>
                <p class="text-gray-500 text-sm">Tampilan: {{ $jenisLabel }}</p>
            </div>

            <div class="grid grid-cols-1 gap-2 md:flex md:flex-wrap w-full md:w-auto">
                <a href="{{ route('laporan-bulanan.pdf', $laporan) }}"
                    class="w-full md:w-auto bg-slate-800 text-white rounded px-4 py-2 text-sm font-semibold text-center">
                    ⬇ PDF Lengkap
                </a>
                <a href="{{ route('laporan-bulanan.pdf-penjualan', $laporan) }}"
                    class="w-full md:w-auto bg-green-600 text-white rounded px-4 py-2 text-sm font-semibold text-center">
                    ⬇ PDF Penjualan
                </a>
                <a href="{{ route('laporan-bulanan.pdf-pembelian', $laporan) }}"
                    class="w-full md:w-auto bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold text-center">
                    ⬇ PDF Pembelian
                </a>
            </div>
        </div>
        @if(session('success'))
            <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="border rounded p-3">
            <p class="text-sm font-semibold text-gray-700 mb-2">Jenis Tampilan</p>
            <div class="flex flex-wrap gap-2">
                @foreach(['lengkap' => 'Rekap Lengkap', 'penjualan' => 'Penjualan', 'pembelian' => 'Pembelian'] as $jenis => $label)
                    <a href="{{ route('laporan-bulanan.show', ['laporan' => $laporan, 'jenis_laporan' => $jenis]) }}"
                        class="rounded px-3 py-1.5 text-xs font-semibold {{ $jenisLaporan === $jenis ? 'bg-blue-600 text-white' : 'border text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Dibuat Oleh</p>
                <p class="font-semibold">{{ $laporan->pembuat->name ?? '-' }}</p>
            </div>
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Dibuat Pada</p>
                <p class="font-semibold">{{ $laporan->created_at ? $laporan->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            <div class="border rounded p-3">
                <p class="text-sm text-gray-500">Ditinjau Pada</p>
                <p class="font-semibold">{{ $laporan->ditinjau_at ? $laporan->ditinjau_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        {{-- REKAP DATA --}}
        <div class="border rounded p-4">
            <h2 class="font-bold mb-3">Rekap Data</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @if($menampilkanPenjualan)
                    <div class="border rounded p-3">
                        <p class="text-xs text-gray-500 uppercase mb-1">Penjualan Toko (Offline)</p>
                        <p class="text-lg font-bold">Rp {{ number_format($laporan->total_penjualan_toko, 0, ',', '.') }}</p>
                    </div>
                    <div class="border rounded p-3">
                        <p class="text-xs text-gray-500 uppercase mb-1">Penjualan Marketplace</p>
                        <p class="text-lg font-bold">Rp {{ number_format($laporan->total_penjualan_marketplace, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="border rounded p-3 bg-slate-50">
                        <p class="text-xs text-gray-500 uppercase mb-1">Total Penjualan
                            ({{ $laporan->jumlah_transaksi_penjualan }} transaksi)</p>
                        <p class="text-lg font-bold text-green-700">Rp
                            {{ number_format($laporan->total_penjualan, 0, ',', '.') }}
                        </p>
                    </div>
                @endif
                @if($menampilkanPembelian)
                    <div class="border rounded p-3 bg-slate-50">
                        <p class="text-xs text-gray-500 uppercase mb-1">Total Pembelian
                            ({{ $laporan->jumlah_transaksi_pembelian }} transaksi)</p>
                        <p class="text-lg font-bold text-blue-700">Rp
                            {{ number_format($laporan->total_pembelian, 0, ',', '.') }}
                        </p>
                    </div>
                @endif
            </div>

            @if($laporanLengkap)
                @php
                    $selisih = $laporan->total_penjualan - $laporan->total_pembelian;
                @endphp
                <div class="mt-3 flex flex-col gap-1 md:flex-row md:justify-between md:items-center bg-slate-900 text-white rounded p-3">
                    <span class="text-sm">Selisih Penjualan &ndash; Pembelian</span>
                    <span class="text-xl font-bold {{ $selisih >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        Rp {{ number_format($selisih, 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>

        {{-- STOK & LABA --}}
        @if($laporanLengkap)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="border rounded p-4">
                <h3 class="font-bold mb-1">⚠️ Stok Menipis</h3>
                <p
                    class="text-3xl font-bold {{ $laporan->jumlah_varian_stok_menipis > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $laporan->jumlah_varian_stok_menipis }}
                </p>
                <p class="text-sm text-gray-500 mt-1">varian dengan stok di bawah/sama dengan batas minimum (per saat
                    laporan dibuat)</p>
            </div>

            <div class="border rounded p-4">
                <h3 class="font-bold mb-1">💰 Estimasi Laba Kotor</h3>
                <p class="text-3xl font-bold {{ $laporan->estimasi_laba_kotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($laporan->estimasi_laba_kotor, 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-500 mt-1">total penjualan dikurangi total pembelian periode ini (estimasi kasar)
                </p>
            </div>

            </div>
        @endif

        {{-- BARANG TERLARIS --}}
        @if($menampilkanPenjualan)
            <div class="border rounded p-4">
            <h2 class="font-bold mb-3">🔥 Barang Terlaris</h2>

            @if(empty($laporan->barang_terlaris))
                <p class="text-sm text-gray-400">Tidak ada data penjualan pada periode ini.</p>
            @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2">Barang</th>
                                <th class="py-2">Varian</th>
                                <th class="py-2">SKU</th>
                                <th class="py-2 text-right">Qty Terjual</th>
                                <th class="py-2 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporan->barang_terlaris as $item)
                                <tr class="border-b">
                                    <td class="py-2">{{ $item['nama_barang'] }}</td>
                                    <td class="py-2">{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td class="py-2 font-mono text-xs">{{ $item['sku'] }}</td>
                                    <td class="py-2 text-right font-semibold">{{ $item['total_qty'] }}</td>
                                    <td class="py-2 text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden space-y-3">
                    @foreach($laporan->barang_terlaris as $item)
                        <div class="border rounded p-3 text-sm space-y-1">
                            <p class="font-semibold break-words">{{ $item['nama_barang'] }}</p>
                            <p class="text-gray-500 break-words">{{ $item['warna'] }} / {{ $item['ukuran'] }}</p>
                            <p class="font-mono text-xs text-gray-500 break-all">{{ $item['sku'] }}</p>
                            <div class="flex justify-between gap-3 pt-1">
                                <span>Qty: <strong>{{ $item['total_qty'] }}</strong></span>
                                <span class="text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            </div>
        @endif

        {{-- BARANG KURANG LAKU --}}
        @if($menampilkanPenjualan)
            <div class="border rounded p-4">
            <h2 class="font-bold mb-3">🐌 Barang Kurang Laku</h2>

            @if(empty($laporan->barang_kurang_laku))
                <p class="text-sm text-gray-400">Tidak ada data penjualan pada periode ini.</p>
            @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2">Barang</th>
                                <th class="py-2">Varian</th>
                                <th class="py-2">SKU</th>
                                <th class="py-2 text-right">Qty Terjual</th>
                                <th class="py-2 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporan->barang_kurang_laku as $item)
                                <tr class="border-b">
                                    <td class="py-2">{{ $item['nama_barang'] }}</td>
                                    <td class="py-2">{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td class="py-2 font-mono text-xs">{{ $item['sku'] }}</td>
                                    <td class="py-2 text-right font-semibold">{{ $item['total_qty'] }}</td>
                                    <td class="py-2 text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden space-y-3">
                    @foreach($laporan->barang_kurang_laku as $item)
                        <div class="border rounded p-3 text-sm space-y-1">
                            <p class="font-semibold break-words">{{ $item['nama_barang'] }}</p>
                            <p class="text-gray-500 break-words">{{ $item['warna'] }} / {{ $item['ukuran'] }}</p>
                            <p class="font-mono text-xs text-gray-500 break-all">{{ $item['sku'] }}</p>
                            <div class="flex justify-between gap-3 pt-1">
                                <span>Qty: <strong>{{ $item['total_qty'] }}</strong></span>
                                <span class="text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            </div>
        @endif

        {{-- RINGKASAN PERENCANAAN PEMBELIAN --}}
        @if($menampilkanPembelian)
            <div class="border rounded p-4">
            <h2 class="font-bold mb-3">📋 Ringkasan Perencanaan Pembelian</h2>

            @php $ringkasan = $laporan->ringkasan_perencanaan ?? []; @endphp

            <p class="text-sm text-gray-600 mb-3">
                Total perencanaan dibuat pada periode ini: <strong>{{ $ringkasan['total_dibuat'] ?? 0 }}</strong>
            </p>

            @if(!empty($ringkasan['per_status']))
                <div class="flex gap-3 flex-wrap">
                    @foreach($ringkasan['per_status'] as $status => $jumlah)
                        <div class="bg-gray-50 border rounded-lg px-4 py-2">
                            <p class="text-xs text-gray-500 uppercase">{{ $status }}</p>
                            <p class="text-lg font-bold">{{ $jumlah }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">Tidak ada perencanaan pembelian pada periode ini.</p>
            @endif
            </div>
        @endif

        {{-- DETAIL TRANSAKSI PENJUALAN --}}
        @if($menampilkanPenjualan)
            <div class="border rounded p-4">
            <h2 class="font-bold mb-3">🧾 Detail Transaksi Penjualan
                ({{ count($laporan->detail_transaksi_penjualan ?? []) }})</h2>

            @if(empty($laporan->detail_transaksi_penjualan))
                <p class="text-sm text-gray-400">Tidak ada transaksi penjualan pada periode ini.</p>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($laporan->detail_transaksi_penjualan as $trx)
                        <div class="border rounded p-3">
                            <div class="flex flex-col gap-1 md:flex-row md:justify-between text-sm font-semibold mb-2 min-w-0">
                                <span class="break-words">#{{ $trx['id'] }} &middot; {{ $trx['tanggal'] }} &middot;
                                    {{ ucfirst($trx['channel']) }}</span>
                                <span class="shrink-0">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                            </div>
                            <table class="hidden md:table w-full text-xs">
                                <thead>
                                    <tr class="text-left text-gray-400 border-b">
                                        <th class="py-1">Barang</th>
                                        <th class="py-1">Varian</th>
                                        <th class="py-1 text-right">Qty</th>
                                        <th class="py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trx['items'] as $item)
                                        <tr class="border-b">
                                            <td class="py-1">{{ $item['nama_barang'] }}</td>
                                            <td class="py-1">{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                            <td class="py-1 text-right">{{ $item['qty'] }}</td>
                                            <td class="py-1 text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="md:hidden space-y-2">
                                @foreach($trx['items'] as $item)
                                    <div class="bg-slate-50 rounded p-2 text-xs space-y-1">
                                        <p class="font-semibold break-words">{{ $item['nama_barang'] }}</p>
                                        <p class="text-gray-500 break-words">{{ $item['warna'] }} / {{ $item['ukuran'] }}</p>
                                        <div class="flex justify-between gap-3">
                                            <span>Qty: {{ $item['qty'] }}</span>
                                            <span class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            </div>
        @endif

        {{-- DETAIL TRANSAKSI PEMBELIAN --}}
        @if($menampilkanPembelian)
            <div class="border rounded p-4">
            <h2 class="font-bold mb-3">📦 Detail Transaksi Pembelian
                ({{ count($laporan->detail_transaksi_pembelian ?? []) }})</h2>

            @if(empty($laporan->detail_transaksi_pembelian))
                <p class="text-sm text-gray-400">Tidak ada transaksi pembelian pada periode ini.</p>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($laporan->detail_transaksi_pembelian as $trx)
                        <div class="border rounded p-3">
                            <div class="flex flex-col gap-1 md:flex-row md:justify-between text-sm font-semibold mb-2 min-w-0">
                                <span class="break-words">#{{ $trx['id'] }} &middot; {{ $trx['tanggal'] }} &middot; {{ $trx['supplier'] }}</span>
                                <span class="shrink-0">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                            </div>
                            <table class="hidden md:table w-full text-xs">
                                <thead>
                                    <tr class="text-left text-gray-400 border-b">
                                        <th class="py-1">Barang</th>
                                        <th class="py-1">Varian</th>
                                        <th class="py-1 text-right">Qty</th>
                                        <th class="py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trx['items'] as $item)
                                        <tr class="border-b">
                                            <td class="py-1">{{ $item['nama_barang'] }}</td>
                                            <td class="py-1">{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                            <td class="py-1 text-right">{{ $item['qty'] }}</td>
                                            <td class="py-1 text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="md:hidden space-y-2">
                                @foreach($trx['items'] as $item)
                                    <div class="bg-slate-50 rounded p-2 text-xs space-y-1">
                                        <p class="font-semibold break-words">{{ $item['nama_barang'] }}</p>
                                        <p class="text-gray-500 break-words">{{ $item['warna'] }} / {{ $item['ukuran'] }}</p>
                                        <div class="flex justify-between gap-3">
                                            <span>Qty: {{ $item['qty'] }}</span>
                                            <span class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            </div>
        @endif

        {{-- CATATAN EVALUASI ADMIN --}}
        @if($laporan->catatan_evaluasi)
            <div class="border rounded p-4">
                <h2 class="font-bold mb-2">Catatan Evaluasi (Admin)</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $laporan->catatan_evaluasi }}</p>
            </div>
        @endif

        {{-- CATATAN OWNER (OPSIONAL, TANPA PROSES KIRIM ADMIN) --}}
        @can('putuskan', $laporan)
            <div class="border border-amber-200 bg-amber-50 rounded p-4">
                <p class="text-sm text-amber-800 mb-3">Tambahkan catatan atau arahan untuk ditindaklanjuti Admin bila diperlukan.</p>
                <form action="{{ route('laporan-bulanan.putuskan', $laporan) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <textarea name="keputusan_owner" rows="4"
                        placeholder="Tulis catatan atau arahan berdasarkan laporan ini..."
                        class="w-full border rounded px-3 py-2 text-sm mb-3" required>{{ old('keputusan_owner') }}</textarea>
                    <button type="submit" class="w-full md:w-auto bg-green-600 text-white rounded px-4 py-2 text-sm font-semibold">Simpan
                        Catatan</button>
                </form>
            </div>
        @endcan

        {{-- CATATAN OWNER (SUDAH DITINJAU, READ-ONLY) --}}
        @if($laporan->status === 'ditinjau')
            <div class="border border-green-200 bg-green-50 rounded p-4">
                <h2 class="font-bold text-green-800 mb-2">Catatan Owner</h2>
                <p class="text-sm text-green-800 whitespace-pre-line">{{ $laporan->keputusan_owner }}</p>
                <p class="text-xs text-green-600 mt-2">Ditinjau pada {{ $laporan->ditinjau_at->format('d M Y H:i') }}</p>
            </div>
        @endif

    </div>
@endsection
