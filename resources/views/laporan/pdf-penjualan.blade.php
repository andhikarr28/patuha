<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .kop {
            width: 100%;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .kop td { border: none; padding: 0; }
        .kop h1 { font-size: 16px; letter-spacing: 0.5px; }
        .kop p { font-size: 11px; color: #444; margin-top: 2px; }
        .kop .doc-title { text-align: right; }
        .kop .doc-title h2 { font-size: 13px; text-transform: uppercase; }
        .kop .doc-title p { font-size: 11px; color: #444; }

        .ringkasan {
            width: 100%;
            margin-bottom: 16px;
        }

        .ringkasan td {
            border: 1px solid #999;
            padding: 8px 10px;
            width: 33.33%;
        }

        .ringkasan .label { font-size: 10px; color: #666; text-transform: uppercase; }
        .ringkasan .value { font-size: 14px; font-weight: bold; margin-top: 2px; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data th, table.data td { border: 1px solid #999; padding: 6px 8px; font-size: 11px; }
        table.data th { background: #f1f5f9; text-align: left; text-transform: uppercase; font-size: 10px; }
        table.data td.right, table.data th.right { text-align: right; }
        table.data tfoot td { font-weight: bold; background: #f8fafc; }

        .footer-doc { margin-top: 24px; font-size: 10px; color: #666; }
    </style>
</head>
<body>

    @php
        $jumlahTransaksi = $penjualan->count();
        $rataRata = $jumlahTransaksi > 0 ? $total / $jumlahTransaksi : 0;
    @endphp

    {{-- KOP --}}
    <table class="kop">
        <tr>
            <td>
                <h1>TOKO PATUHA OUTDOOR</h1>
                <p>Jl. Contoh No. 123, Bandung</p>
            </td>
            <td class="doc-title">
                <h2>Laporan Penjualan</h2>
                @if(isset($tanggalAwal) && isset($tanggalAkhir))
                    <p>{{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}</p>
                @endif
            </td>
        </tr>
    </table>

    {{-- RINGKASAN --}}
    <table class="ringkasan">
        <tr>
            <td>
                <p class="label">Total Transaksi</p>
                <p class="value">{{ number_format($jumlahTransaksi) }} transaksi</p>
            </td>
            <td>
                <p class="label">Total Penjualan</p>
                <p class="value">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </td>
            <td>
                <p class="label">Rata-rata / Transaksi</p>
                <p class="value">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
            </td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table class="data">
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

    <p class="footer-doc">Dicetak pada {{ now()->format('d F Y H:i') }} WIB &mdash; Sistem Toko Patuha Outdoor</p>

</body>
</html>