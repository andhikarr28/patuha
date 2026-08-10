<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $laporan->kode_laporan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e2e8f0; padding: 4px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .summary-box { display: table; width: 100%; margin-top: 8px; }
        .summary-cell { display: table-cell; width: 25%; padding: 8px; border: 1px solid #e2e8f0; }
        .summary-label { font-size: 9px; text-transform: uppercase; color: #64748b; }
        .summary-value { font-size: 13px; font-weight: bold; margin-top: 2px; }
        .trx-block { margin-bottom: 10px; border: 1px solid #e2e8f0; padding: 6px; }
        .trx-header { font-weight: bold; margin-bottom: 4px; }
        .page-break { page-break-before: always; }
        .badge { font-size: 9px; padding: 1px 6px; border: 1px solid #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body>

    <h1>{{ $laporan->kode_laporan }}</h1>
    <p class="muted">
        @if($mode === 'penjualan')
            Laporan Penjualan &mdash;
        @elseif($mode === 'pembelian')
            Laporan Pembelian &mdash;
        @endif
        Periode {{ $laporan->periode_awal->format('d M Y') }} &mdash; {{ $laporan->periode_akhir->format('d M Y') }}<br>
        Dibuat oleh: {{ $laporan->pembuat->name ?? '-' }}
    </p>

    {{-- RINGKASAN --}}
    <h2>Ringkasan</h2>
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
        <h2>Barang Terlaris</h2>
        @if(empty($laporan->barang_terlaris))
            <p class="muted">Tidak ada data.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Varian</th>
                        <th>SKU</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->barang_terlaris as $item)
                        <tr>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                            <td>{{ $item['sku'] }}</td>
                            <td class="text-right">{{ $item['total_qty'] }}</td>
                            <td class="text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- BARANG KURANG LAKU --}}
        <h2>Barang Kurang Laku</h2>
        @if(empty($laporan->barang_kurang_laku))
            <p class="muted">Tidak ada data.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Varian</th>
                        <th>SKU</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->barang_kurang_laku as $item)
                        <tr>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                            <td>{{ $item['sku'] }}</td>
                            <td class="text-right">{{ $item['total_qty'] }}</td>
                            <td class="text-right">Rp {{ number_format($item['total_omzet'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- DETAIL TRANSAKSI PENJUALAN --}}
        <div class="page-break"></div>
        <h2>Detail Transaksi Penjualan ({{ count($laporan->detail_transaksi_penjualan ?? []) }} transaksi)</h2>

        @if(empty($laporan->detail_transaksi_penjualan))
            <p class="muted">Tidak ada transaksi penjualan pada periode ini.</p>
        @else
            @foreach($laporan->detail_transaksi_penjualan as $trx)
                <div class="trx-block">
                    <div class="trx-header">
                        #{{ $trx['id'] }} &mdash; {{ $trx['tanggal'] }} &mdash; {{ ucfirst($trx['channel']) }}
                        <span style="float:right;">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trx['items'] as $item)
                                <tr>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td class="text-right">{{ $item['qty'] }}</td>
                                    <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
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
        <h2>Detail Transaksi Pembelian ({{ count($laporan->detail_transaksi_pembelian ?? []) }} transaksi)</h2>

        @if(empty($laporan->detail_transaksi_pembelian))
            <p class="muted">Tidak ada transaksi pembelian pada periode ini.</p>
        @else
            @foreach($laporan->detail_transaksi_pembelian as $trx)
                <div class="trx-block">
                    <div class="trx-header">
                        #{{ $trx['id'] }} &mdash; {{ $trx['no_faktur'] ?? '-' }} &mdash; {{ $trx['tanggal'] }} &mdash; {{ $trx['supplier'] }}
                        <span class="badge">{{ ucfirst($trx['status'] ?? '-') }}</span>
                        <span style="float:right;">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trx['items'] as $item)
                                <tr>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>{{ $item['warna'] }} / {{ $item['ukuran'] }}</td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td class="text-right">{{ $item['qty'] }}</td>
                                    <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
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
            <h2>Catatan Evaluasi</h2>
            <p>{{ $laporan->catatan_evaluasi }}</p>
        @endif

        @if($laporan->status === 'ditinjau' && $laporan->keputusan_owner)
            <h2>Keputusan Owner</h2>
            <p>{{ $laporan->keputusan_owner }}</p>
        @endif
    @endif

</body>
</html>