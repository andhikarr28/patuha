<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk {{ $penjualan->no_nota }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 12px;
        color: #000;
        background: #e5e7eb;
        padding: 24px 0;
    }

    .struk {
        width: 302px; /* ~80mm printer kasir */
        margin: 0 auto;
        background: #fff;
        padding: 16px;
    }

    .center { text-align: center; }
    .right { text-align: right; }
    .bold { font-weight: bold; }

    .line {
        border-top: 1px dashed #000;
        margin: 8px 0;
    }

    .row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .item { margin-bottom: 6px; }
    .item-name { font-weight: bold; }
    .item-sub { font-size: 11px; color: #333; }

    table { width: 100%; border-collapse: collapse; }
    td { padding: 2px 0; vertical-align: top; }

    .actions {
        width: 302px;
        margin: 16px auto 0;
        text-align: center;
    }

    .actions button,
    .actions a {
        display: inline-block;
        font-family: -apple-system, sans-serif;
        font-size: 14px;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        margin: 4px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-print { background: #2563eb; color: #fff; }
    .btn-back { background: #e2e8f0; color: #334155; }

    @media print {
        body { background: #fff; padding: 0; }
        .struk { width: 100%; padding: 0; }
        .actions { display: none; }
        @page { margin: 0; size: 80mm auto; }
    }
</style>
</head>
<body>

    <div class="struk">

        <div class="center">
            <p class="bold" style="font-size: 14px;">TOKO PATUHA OUTDOOR</p>
            <p>Jl. Terusan Kopo No. 13, Katapang</p>
            <p>0811-2278-811</p>
        </div>

        <div class="line"></div>

        <table>
            <tr><td>No Nota</td><td class="right">{{ $penjualan->no_nota }}</td></tr>
            <tr><td>Tanggal</td><td class="right">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Kasir</td><td class="right">{{ $penjualan->user->name ?? '-' }}</td></tr>
            <tr><td>Channel</td><td class="right">{{ ucfirst($penjualan->channel) }}</td></tr>
        </table>

        <div class="line"></div>

        @foreach($detail as $item)
            <div class="item">
                <div class="item-name">{{ $item->varian->barang->nama_barang ?? 'Barang' }}</div>
                <div class="item-sub">
                    {{ $item->varian->warna ?? '-' }}{{ $item->varian->ukuran ? ' / ' . $item->varian->ukuran : '' }}
                </div>
                <div class="row">
                    <span>{{ $item->qty }} x {{ number_format($item->harga, 0, ',', '.') }}</span>
                    <span class="bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="line"></div>

        <table>
            <tr>
                <td class="bold" style="font-size: 14px;">TOTAL</td>
                <td class="right bold" style="font-size: 14px;">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="right">{{ ucfirst($penjualan->metode_pembayaran) }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="center">
            <p>Terima kasih telah berbelanja</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar</p>
        </div>

    </div>

    <div class="actions">
        <button type="button" class="btn-print" onclick="window.print()">🖨 Cetak Struk</button>
        <a href="{{ route('penjualan.index') }}" class="btn-back">Kembali</a>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>