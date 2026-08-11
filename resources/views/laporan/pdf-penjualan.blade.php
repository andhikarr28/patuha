<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        @include('pdf.partials.style')
    </style>
</head>
<body>

    @php
        $docTitle = 'Laporan Penjualan';
        $docLines = isset($tanggalAwal, $tanggalAkhir)
            ? [\Carbon\Carbon::parse($tanggalAwal)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')]
            : [];

        $jumlahTransaksi = $penjualan->count();
        $rataRata = $jumlahTransaksi > 0 ? $total / $jumlahTransaksi : 0;
    @endphp

    @include('pdf.partials.kop')

    {{-- RINGKASAN --}}
    <div class="summary-box">
        <div class="summary-cell">
            <div class="summary-label">Total Transaksi</div>
            <div class="summary-value">{{ number_format($jumlahTransaksi) }} transaksi</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Total Penjualan</div>
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
                <th>No Nota</th>
                <th>Tanggal</th>
                <th>Channel</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->no_nota }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}</td>
                    <td>{{ ucfirst($item->channel) }}</td>
                    <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 16px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right">TOTAL PENJUALAN</td>
                <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="footer-doc">Dicetak pada {{ now()->format('d F Y H:i') }} WIB &middot; Sistem Toko Patuha Outdoor</p>

</body>
</html>