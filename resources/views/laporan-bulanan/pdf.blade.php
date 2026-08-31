<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $laporan->kode_laporan }}</title>
    <style>
        @include('pdf.partials.style')
    </style>
</head>
<body>

    @php
        $docTitle = match($mode) {
            'penjualan' => 'Laporan Bulanan — Penjualan',
            'pembelian' => 'Laporan Bulanan — Pembelian',
            default => 'Laporan Bulanan',
        };
        $docLines = [
            $laporan->kode_laporan,
            $laporan->periode_awal->format('d M Y') . ' — ' . $laporan->periode_akhir->format('d M Y'),
        ];
    @endphp

    @include('pdf.partials.kop')

    <p class="muted" style="margin-bottom:14px;">Dibuat oleh: {{ $laporan->pembuat->name ?? '-' }}</p>

    {{-- RINGKASAN --}}
    <h2 class="section-title">Ringkasan</h2>
    <div class="summary-box">
        @if($mode !== 'pembelian')
            <div class="summary-cell">
                <div class="summary-label">Penjualan Toko</div>
                <div class="summary-value">Rp {{ number_format($laporan->total_penjualan_toko, 0, ',', '.') }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Penjualan Marketplace</div>
                <div class="summary-value">Rp {{ number_format($laporan->total_penjualan_marketplace, 0, ',', '.') }}</div>
            </div>
        @endif

        @if($mode !== 'penjualan')
            <div class="summary-cell">
                <div class="summary-label">Total Pembelian</div>
                <div class="summary-value">Rp {{ number_format($laporan->total_pembelian, 0, ',', '.') }}</div>
            </div>
        @endif

        @if($mode === 'full')
            <div class="summary-cell">
                <div class="summary-label">Estimasi Laba Kotor</div>
                <div class="summary-value">Rp {{ number_format($laporan->estimasi_laba_kotor, 0, ',', '.') }}</div>
            </div>
        @endif
    </div>

    @if($mode !== 'pembelian')

        {{-- BARANG TERLARIS --}}
        <h2 class="section-title">Barang Terlaris</h2>
        @if(empty($laporan->barang_terlaris))
            <p class="muted">Tidak ada data.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Varian</th>
                        <th>SKU</th>
                        <th class="right">Qty</th>
                        <th class="right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->barang_terlaris as $item)
                        <tr>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                            <td>{{ $item['sku'] }}</td>
                            <td class="right">{{ $item['total_qty'] }}</td>
                            <td class="right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- BARANG KURANG LAKU --}}
        <h2 class="section-title">Barang Kurang Laku</h2>
        @if(empty($laporan->barang_kurang_laku))
            <p class="muted">Tidak ada data.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Varian</th>
                        <th>SKU</th>
                        <th class="right">Qty</th>
                        <th class="right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->barang_kurang_laku as $item)
                        <tr>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                            <td>{{ $item['sku'] }}</td>
                            <td class="right">{{ $item['total_qty'] }}</td>
                            <td class="right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- DETAIL TRANSAKSI PENJUALAN --}}
        <div class="page-break"></div>
        <h2 class="section-title">Detail Transaksi Penjualan ({{ count($laporan->detail_transaksi_penjualan ?? []) }} transaksi)</h2>

        @if(empty($laporan->detail_transaksi_penjualan))
            <p class="muted">Tidak ada transaksi penjualan pada periode ini.</p>
        @else
            @foreach($laporan->detail_transaksi_penjualan as $trx)
                <div class="trx-block">
                    <div class="trx-header">
                        #{{ $trx['id'] }} &middot; {{ $trx['tanggal'] }} &middot; {{ ucfirst($trx['channel']) }}
                        <span style="float:right;">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th class="right">Qty</th>
                                <th class="right">Harga</th>
                                <th class="right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trx['items'] as $item)
                                <tr>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td class="right">{{ $item['qty'] }}</td>
                                    <td class="right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

    @endif

    @if($mode !== 'penjualan')

        {{-- DETAIL TRANSAKSI PEMBELIAN --}}
        @if($mode === 'full')
            <div class="page-break"></div>
        @endif
        <h2 class="section-title">Detail Transaksi Pembelian ({{ count($laporan->detail_transaksi_pembelian ?? []) }} transaksi)</h2>

        @if(empty($laporan->detail_transaksi_pembelian))
            <p class="muted">Tidak ada transaksi pembelian pada periode ini.</p>
        @else
            @foreach($laporan->detail_transaksi_pembelian as $trx)
                <div class="trx-block">
                    <div class="trx-header">
                        #{{ $trx['id'] }} &middot; {{ $trx['no_faktur'] ?? '-' }} &middot; {{ $trx['tanggal'] }} &middot; {{ $trx['supplier'] }}
                        <span class="badge">{{ ucfirst($trx['status'] ?? '-') }}</span>
                        <span style="float:right;">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th class="right">Qty</th>
                                <th class="right">Harga</th>
                                <th class="right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trx['items'] as $item)
                                <tr>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td class="right">{{ $item['qty'] }}</td>
                                    <td class="right">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

    @endif

    @if($mode === 'full')
        @if($laporan->catatan_evaluasi)
            <h2 class="section-title">Catatan Evaluasi</h2>
            <p>{{ $laporan->catatan_evaluasi }}</p>
        @endif

        @if($laporan->status === 'ditinjau' && $laporan->keputusan_owner)
            <h2 class="section-title">Catatan Owner</h2>
            <p>{{ $laporan->keputusan_owner }}</p>
        @endif
    @endif

    <p class="footer-doc">Dicetak pada {{ now()->format('d F Y H:i') }} WIB &middot; Sistem Toko Patuha Outdoor</p>

</body>
</html>
