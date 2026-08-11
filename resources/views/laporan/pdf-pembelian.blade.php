<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian</title>
    <style>
        @include('pdf.partials.style')
    </style>
</head>
<body>

    @php
        $docTitle = 'Laporan Pembelian';
        $docLines = isset($tanggalAwal, $tanggalAkhir)
            ? [\Carbon\Carbon::parse($tanggalAwal)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')]
            : [];

        $jumlahTransaksi = $pembelian->count();
        $rataRata = $jumlahTransaksi > 0 ? $total / $jumlahTransaksi : 0;
    @endphp

    @include('pdf.partials.kop')

    {{-- RINGKASAN --}}
    <div class="summary-box">
        <div class="summary-cell">
            <div class="summary-label">Total Transaksi</div>
            <div class="summary-value">{{ number_format($jumlahTransaksi) }} pembelian</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Total Pembelian</div>
            <div class="summary-value">Rp {{ number_format($total, 0, ',', '.') }}</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Rata-rata / Transaksi</div>
            <div class="summary-value">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th>Faktur</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th class="right">Total Netto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelian as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->no_faktur }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}</td>
                    <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($item->total_netto, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 16px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right">TOTAL PEMBELIAN</td>
                <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="footer-doc">Dicetak pada {{ now()->format('d F Y H:i') }} WIB &middot; Sistem Toko Patuha Outdoor</p>

</body>
</html>