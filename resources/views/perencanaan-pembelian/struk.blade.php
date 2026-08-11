<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Pesanan Pembelian - {{ $perencanaan->no_perencanaan }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        color: #1e293b;
        background: #e5e7eb;
        padding: 24px 0;
    }

    .lembar {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        background: #fff;
        padding: 20mm 18mm;
    }

    /* KOP SURAT */
    .kop {
        display: flex;
        align-items: center;
        border-bottom: 3px solid #0f172a;
        padding-bottom: 14px;
        margin-bottom: 22px;
    }

    .kop .logo-box {
        width: 56px;
        height: 56px;
        flex-shrink: 0;
        margin-right: 14px;
    }

    .kop .logo-box img {
        width: 56px;
        height: 56px;
        object-fit: contain;
    }

    .kop .logo-fallback {
        width: 56px;
        height: 56px;
        background: #0f172a;
        color: #fff;
        text-align: center;
        line-height: 56px;
        font-weight: bold;
        font-size: 22px;
        border-radius: 6px;
    }

    .kop .brand h1 { font-size: 19px; letter-spacing: 0.5px; color: #0f172a; }
    .kop .brand p { font-size: 11.5px; color: #475569; margin-top: 2px; }

    .kop .doc-title { text-align: right; margin-left: auto; }
    .kop .doc-title h2 { font-size: 15px; text-transform: uppercase; color: #0f172a; }
    .kop .doc-title p { font-size: 11.5px; color: #475569; margin-top: 2px; }

    /* TUJUAN + INFO */
    .top-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 24px;
        margin-bottom: 20px;
    }

    .tujuan p.label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
    .tujuan p.nama { font-weight: bold; font-size: 14px; color: #0f172a; }
    .tujuan p { font-size: 12px; color: #334155; }

    .info-list { font-size: 13px; }
    .info-list .label { display: inline-block; width: 140px; color: #64748b; }

    /* TABLE BARANG */
    table.barang {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }

    table.barang th,
    table.barang td {
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        font-size: 12px;
    }

    table.barang th {
        background: #f1f5f9;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        color: #334155;
    }

    table.barang td.right, table.barang th.right { text-align: right; }
    table.barang td.center, table.barang th.center { text-align: center; }

    table.barang tfoot td { font-weight: bold; background: #f8fafc; }

    .disclaimer {
        font-size: 11px;
        color: #64748b;
        font-style: italic;
        margin-bottom: 16px;
    }

    /* CATATAN */
    .catatan-box {
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 10px 12px;
        font-size: 12px;
        color: #334155;
        margin-bottom: 30px;
        background: #fafafa;
    }

    .catatan-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }

    /* TANDA TANGAN */
    .ttd {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 30px;
        text-align: center;
        font-size: 13px;
    }

    .ttd .kotak {
        border-top: 1px solid #0f172a;
        margin: 60px 20px 4px;
        padding-top: 6px;
    }

    .footer-doc {
        margin-top: 24px;
        font-size: 10px;
        color: #94a3b8;
    }

    /* ACTIONS (tidak ikut cetak) */
    .actions {
        width: 210mm;
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
        .lembar { width: 100%; min-height: 0; padding: 12mm 15mm; }
        .actions { display: none; }
        @page { size: A4; margin: 0; }
    }
</style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo-patuha.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <div class="lembar">

        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="logo-box">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo Patuha">
                @else
                    <div class="logo-fallback">P</div>
                @endif
            </div>
            <div class="brand">
                <h1>TOKO PATUHA OUTDOOR</h1>
                <p>Jl. Terusan Kopo No.13 &middot; 0811-2278-811</p>
            </div>
            <div class="doc-title">
                <h2>Surat Pesanan Pembelian</h2>
                <p>No. {{ $perencanaan->no_perencanaan }}</p>
                <p>Tanggal {{ \Carbon\Carbon::parse($perencanaan->tanggal_perencanaan)->format('d F Y') }}</p>
            </div>
        </div>

        {{-- TUJUAN + INFO --}}
        <div class="top-grid">
            <div class="tujuan">
                <p class="label">Kepada Yth.</p>
                <p class="nama">{{ $perencanaan->supplier->nama_supplier ?? '-' }}</p>
                <p>{{ $perencanaan->supplier->alamat ?? '-' }}</p>
                <p>{{ $perencanaan->supplier->no_hp ?? '-' }}</p>
            </div>

            <div class="info-list">
                <p><span class="label">No. Pesanan</span>: {{ $perencanaan->no_perencanaan }}</p>
                <p><span class="label">Tanggal Pesanan</span>: {{ \Carbon\Carbon::parse($perencanaan->tanggal_perencanaan)->format('d F Y') }}</p>
                <p><span class="label">Dibuat Oleh</span>: {{ $perencanaan->user->name ?? '-' }}</p>
                <p><span class="label">Status</span>: {{ ucfirst(str_replace('_', ' ', $perencanaan->status)) }}</p>
            </div>
        </div>

        <p style="font-size: 13px; margin-bottom: 12px;">
            Dengan ini kami mengajukan pesanan pembelian barang kepada supplier sebagaimana rincian di bawah ini.
            Mohon konfirmasi ketersediaan dan estimasi waktu pengiriman.
        </p>

        {{-- TABEL BARANG --}}
        <table class="barang">
            <thead>
                <tr>
                    <th style="width: 24px;">No</th>
                    <th>Nama Barang</th>
                    <th>Varian</th>
                    <th>SKU</th>
                    <th class="center" style="width: 60px;">Qty</th>
                    <th class="right" style="width: 95px;">Estimasi Harga</th>
                    <th class="right" style="width: 100px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perencanaan->details as $index => $detail)
                    @php
                        $subtotal = $detail->qty_rencana * $detail->estimasi_harga;
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $detail->varian->barang->nama_barang ?? 'Barang' }}</td>
                        <td>{{ $detail->varian->warna ?? '-' }}{{ $detail->varian->ukuran ? ' / ' . $detail->varian->ukuran : '' }}</td>
                        <td>{{ $detail->varian->sku ?? '-' }}</td>
                        <td class="center">{{ $detail->qty_rencana }}</td>
                        <td class="right">{{ number_format($detail->estimasi_harga, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="right" style="font-size: 13px;">ESTIMASI TOTAL</td>
                    <td class="right" style="font-size: 13px;">
                        Rp {{ number_format($perencanaan->details->sum(fn($d) => $d->qty_rencana * $d->estimasi_harga), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <p class="disclaimer">
            * Harga di atas adalah estimasi dan dapat berubah menyesuaikan harga aktual dari supplier saat barang dikirim.
        </p>

        {{-- CATATAN --}}
        @if($perencanaan->catatan)
            <div class="catatan-box">
                <p class="label">Catatan</p>
                <p>{{ $perencanaan->catatan }}</p>
            </div>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div>
                <p>Hormat kami,</p>
                <div class="kotak">
                    ( {{ $perencanaan->user->name ?? '.....................' }} )
                </div>
                <p style="font-size: 11px; color: #64748b;">Toko Patuha Outdoor</p>
            </div>
            <div>
                <p>Disetujui oleh Supplier,</p>
                <div class="kotak">
                    ( {{ $perencanaan->supplier->nama_supplier ?? '.....................' }} )
                </div>
                <p style="font-size: 11px; color: #64748b;">Tanda tangan &amp; stempel</p>
            </div>
        </div>

        <p class="footer-doc">Dicetak pada {{ now()->format('d F Y H:i') }} WIB &middot; Sistem Toko Patuha Outdoor</p>

    </div>

    <div class="actions">
        <button type="button" class="btn-print" onclick="window.print()">🖨 Cetak Surat Pesanan</button>
        <a href="{{ route('perencanaan-pembelian.show', $perencanaan->id) }}" class="btn-back">Kembali</a>
    </div>

</body>
</html>