<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Penerimaan Barang - {{ $pembelian->no_faktur }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        color: #111;
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
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 3px solid #111;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .kop h1 {
        font-size: 20px;
        letter-spacing: 1px;
    }

    .kop p {
        font-size: 12px;
        color: #444;
        margin-top: 2px;
    }

    .kop .doc-title {
        text-align: right;
    }

    .kop .doc-title h2 {
        font-size: 16px;
        text-transform: uppercase;
    }

    .kop .doc-title p {
        font-size: 12px;
        color: #444;
    }

    /* INFO */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px 24px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .info-grid .label {
        display: inline-block;
        width: 130px;
        color: #555;
    }

    /* TABLE BARANG */
    table.barang {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    table.barang th,
    table.barang td {
        border: 1px solid #999;
        padding: 6px 8px;
        font-size: 12px;
    }

    table.barang th {
        background: #f1f5f9;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
    }

    table.barang td.right,
    table.barang th.right { text-align: right; }

    table.barang td.center,
    table.barang th.center { text-align: center; }

    table.barang tfoot td {
        font-weight: bold;
        background: #f8fafc;
    }

    /* CATATAN */
    .catatan {
        margin-bottom: 30px;
        font-size: 12px;
        color: #444;
    }

    /* TANDA TANGAN */
    .ttd {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 40px;
        text-align: center;
        font-size: 13px;
    }

    .ttd .kotak {
        border-top: 1px solid #111;
        margin: 60px 20px 4px;
        padding-top: 6px;
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

    <div class="lembar">

        {{-- KOP SURAT --}}
        <div class="kop">
            <div>
                <h1>TOKO PATUHA OUTDOOR</h1>
                <p>Jl. Contoh No. 123, Bandung</p>
                <p>0812-xxxx-xxxx</p>
            </div>
            <div class="doc-title">
                <h2>Surat Penerimaan Barang</h2>
                <p>No. {{ $pembelian->no_faktur }}</p>
            </div>
        </div>

        {{-- INFORMASI --}}
        <div class="info-grid">
            <div><span class="label">Tanggal Penerimaan</span>: {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d F Y') }}</div>
            <div><span class="label">Diterima Oleh</span>: {{ $pembelian->user->name ?? '-' }}</div>
            <div><span class="label">Supplier</span>: {{ $pembelian->supplier->nama_supplier ?? '-' }}</div>
            <div><span class="label">No. Referensi</span>: {{ $pembelian->perencanaan->no_perencanaan ?? '-' }}</div>
            <div><span class="label">Alamat Supplier</span>: {{ $pembelian->supplier->alamat ?? '-' }}</div>
            <div><span class="label">No HP Supplier</span>: {{ $pembelian->supplier->no_hp ?? '-' }}</div>
        </div>

        {{-- TABEL BARANG --}}
        <table class="barang">
            <thead>
                <tr>
                    <th style="width: 24px;">No</th>
                    <th>Nama Barang</th>
                    <th>Varian</th>
                    <th>SKU</th>
                    <th class="center" style="width: 50px;">Qty</th>
                    <th class="right" style="width: 90px;">Harga Satuan</th>
                    <th class="right" style="width: 80px;">Diskon</th>
                    <th class="right" style="width: 100px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail as $index => $item)
                    @php
                        $brutto = $item->qty * $item->harga_satuan;
                        $netto = max(0, $brutto - $item->diskon);
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->varian->barang->nama_barang ?? 'Barang' }}</td>
                        <td>{{ $item->varian->warna ?? '-' }}{{ $item->varian->ukuran ? ' / ' . $item->varian->ukuran : '' }}</td>
                        <td>{{ $item->varian->sku ?? '-' }}</td>
                        <td class="center">{{ $item->qty }}</td>
                        <td class="right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="right">{{ $item->diskon > 0 ? number_format($item->diskon, 0, ',', '.') : '-' }}</td>
                        <td class="right">{{ number_format($netto, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="right">Total Brutto</td>
                    <td class="right">Rp {{ number_format($pembelian->total_brutto, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="right">Total Diskon</td>
                    <td class="right">- Rp {{ number_format($pembelian->total_diskon, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="right" style="font-size: 13px;">TOTAL NETTO</td>
                    <td class="right" style="font-size: 13px;">Rp {{ number_format($pembelian->total_netto, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- CATATAN --}}
        <p class="catatan">
            Barang tersebut di atas telah diperiksa jumlah dan kondisinya, serta diterima dengan baik oleh pihak toko
            pada tanggal yang tercantum. Surat ini menjadi bukti sah penerimaan barang dari supplier.
        </p>

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div>
                <p>Yang Menyerahkan,</p>
                <div class="kotak">
                    ( {{ $pembelian->supplier->nama_supplier ?? '.....................' }} )
                </div>
            </div>
            <div>
                <p>Yang Menerima,</p>
                <div class="kotak">
                    ( {{ $pembelian->user->name ?? '.....................' }} )
                </div>
            </div>
        </div>

    </div>

    <div class="actions">
        <button type="button" class="btn-print" onclick="window.print()">🖨 Cetak Surat</button>
        <a href="{{ route('pembelian.index') }}" class="btn-back">Kembali</a>
    </div>

</body>
</html>