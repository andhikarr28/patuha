<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\VarianBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanBulananController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Laporan::class);

        $query = Laporan::with('pembuat')->latest();

        if (auth()->user()->hasRole('owner')) {
            $query->whereIn('status', ['terkirim', 'ditinjau']);
        }

        $laporan = $query->get();

        return view('laporan-bulanan.index', compact('laporan'));
    }

    public function create()
    {
        $this->authorize('create', Laporan::class);

        return view('laporan-bulanan.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Laporan::class);

        $validated = $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        $awal = $validated['periode_awal'];
        $akhir = $validated['periode_akhir'];

        /*
        |--------------------------------------------------------------------------
        | Penjualan & Pembelian (rekap dasar)
        |--------------------------------------------------------------------------
        */

        $penjualanToko = Penjualan::whereBetween('tanggal_penjualan', [$awal, $akhir])
            ->where('channel', 'offline');

        $penjualanMp = Penjualan::whereBetween('tanggal_penjualan', [$awal, $akhir])
            ->where('channel', '!=', 'offline');

        $pembelian = Pembelian::whereBetween('tanggal_pembelian', [$awal, $akhir]);

        $totalPenjualanToko = (clone $penjualanToko)->sum('total');
        $totalPenjualanMp = (clone $penjualanMp)->sum('total');
        $totalPembelian = (clone $pembelian)->sum('total_netto');

        /*
        |--------------------------------------------------------------------------
        | Barang Terlaris & Kurang Laku (agregat dari detail_penjualan)
        |--------------------------------------------------------------------------
        */

        $baseQueryDetail = DB::table('detail_penjualan')
            ->join('penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
            ->join('varian_barang', 'varian_barang.id', '=', 'detail_penjualan.varian_id')
            ->join('barang', 'barang.id', '=', 'varian_barang.barang_id')
            ->whereBetween('penjualan.tanggal_penjualan', [$awal, $akhir])
            ->select(
                'varian_barang.id as varian_id',
                'varian_barang.warna',
                'varian_barang.ukuran',
                'varian_barang.sku',
                'barang.id as barang_id',
                'barang.nama_barang',
                'barang.kode_barang',
                DB::raw('SUM(detail_penjualan.qty) as total_qty'),
                DB::raw('SUM(detail_penjualan.subtotal) as total_omzet')
            )
            ->groupBy(
                'varian_barang.id',
                'varian_barang.warna',
                'varian_barang.ukuran',
                'varian_barang.sku',
                'barang.id',
                'barang.nama_barang',
                'barang.kode_barang'
            );

        $barangTerlaris = (clone $baseQueryDetail)
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => $this->formatBarisAgregat($row))
            ->toArray();

        $barangKurangLaku = (clone $baseQueryDetail)
            ->orderBy('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => $this->formatBarisAgregat($row))
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Detail Transaksi Penjualan (per transaksi, lengkap dengan barangnya)
        |--------------------------------------------------------------------------
        */

        $daftarPenjualan = Penjualan::whereBetween('tanggal_penjualan', [$awal, $akhir])
            ->orderBy('tanggal_penjualan')
            ->get();

        $detailTransaksiPenjualan = [];

        foreach ($daftarPenjualan as $trx) {

            $items = DB::table('detail_penjualan')
                ->join('varian_barang', 'varian_barang.id', '=', 'detail_penjualan.varian_id')
                ->join('barang', 'barang.id', '=', 'varian_barang.barang_id')
                ->where('detail_penjualan.penjualan_id', $trx->id)
                ->select(
                    'barang.nama_barang',
                    'varian_barang.warna',
                    'varian_barang.ukuran',
                    'varian_barang.sku',
                    'detail_penjualan.qty',
                    'detail_penjualan.harga',
                    'detail_penjualan.subtotal'
                )
                ->get();

            $detailTransaksiPenjualan[] = [
                'id'       => $trx->id,
                'tanggal'  => $trx->tanggal_penjualan instanceof \Carbon\Carbon
                    ? $trx->tanggal_penjualan->format('Y-m-d H:i')
                    : (string) $trx->tanggal_penjualan,
                'channel'  => $trx->channel,
                'total'    => (float) $trx->total,
                'items'    => $items->map(fn ($i) => [
                    'nama_barang' => $i->nama_barang,
                    'warna'       => $i->warna,
                    'ukuran'      => $i->ukuran,
                    'sku'         => $i->sku,
                    'qty'         => (int) $i->qty,
                    'harga'       => (float) $i->harga,
                    'subtotal'    => (float) $i->subtotal,
                ])->toArray(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Detail Transaksi Pembelian (per transaksi, lengkap dengan barangnya)
        |--------------------------------------------------------------------------
        */

        $daftarPembelian = Pembelian::with('supplier')
            ->whereBetween('tanggal_pembelian', [$awal, $akhir])
            ->orderBy('tanggal_pembelian')
            ->get();

        $detailTransaksiPembelian = [];

        foreach ($daftarPembelian as $trx) {

            $items = DB::table('detail_pembelian')
                ->join('varian_barang', 'varian_barang.id', '=', 'detail_pembelian.varian_id')
                ->join('barang', 'barang.id', '=', 'varian_barang.barang_id')
                ->where('detail_pembelian.pembelian_id', $trx->id)
                ->select(
                    'barang.nama_barang',
                    'varian_barang.warna',
                    'varian_barang.ukuran',
                    'varian_barang.sku',
                    'detail_pembelian.qty',
                    'detail_pembelian.harga_satuan',
                    'detail_pembelian.subtotal'
                )
                ->get();

            $detailTransaksiPembelian[] = [
                'id'         => $trx->id,
                'no_faktur'  => $trx->no_faktur,
                'tanggal'    => $trx->tanggal_pembelian instanceof \Carbon\Carbon
                    ? $trx->tanggal_pembelian->format('Y-m-d H:i')
                    : (string) $trx->tanggal_pembelian,
                'supplier'   => $trx->supplier->nama_supplier ?? '-',
                'status'     => $trx->status,
                'total'      => (float) $trx->total_netto,
                'items'      => $items->map(fn ($i) => [
                    'nama_barang' => $i->nama_barang,
                    'warna'       => $i->warna,
                    'ukuran'      => $i->ukuran,
                    'sku'         => $i->sku,
                    'qty'         => (int) $i->qty,
                    'harga_satuan'       => (float) $i->harga_satuan,
                    'subtotal'    => (float) $i->subtotal,
                ])->toArray(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Perencanaan Pembelian
        |--------------------------------------------------------------------------
        */

        $totalPerencanaan = DB::table('perencanaan_pembelian')
            ->whereBetween('tanggal_perencanaan', [$awal, $akhir])
            ->count();

        $perencanaanPerStatus = DB::table('perencanaan_pembelian')
            ->whereBetween('tanggal_perencanaan', [$awal, $akhir])
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $ringkasanPerencanaan = [
            'total_dibuat' => $totalPerencanaan,
            'per_status'   => $perencanaanPerStatus,
        ];

        /*
        |--------------------------------------------------------------------------
        | Stok Menipis (snapshot saat laporan dibuat)
        |--------------------------------------------------------------------------
        */

        $jumlahVarianStokMenipis = VarianBarang::whereColumn('stok', '<=', 'stok_minimum')->count();

        /*
        |--------------------------------------------------------------------------
        | Estimasi Laba Kotor
        |--------------------------------------------------------------------------
        */

        $estimasiLabaKotor = ($totalPenjualanToko + $totalPenjualanMp) - $totalPembelian;

        /*
        |--------------------------------------------------------------------------
        | Simpan Laporan
        |--------------------------------------------------------------------------
        */

        $laporan = Laporan::create([
            'kode_laporan' => 'LAP-' . now()->format('Ym') . '-' . str_pad(Laporan::count() + 1, 3, '0', STR_PAD_LEFT),
            'periode_awal' => $awal,
            'periode_akhir' => $akhir,
            'jumlah_transaksi_penjualan' => (clone $penjualanToko)->count() + (clone $penjualanMp)->count(),
            'total_penjualan_toko' => $totalPenjualanToko,
            'total_penjualan_marketplace' => $totalPenjualanMp,
            'jumlah_transaksi_pembelian' => (clone $pembelian)->count(),
            'total_pembelian' => $totalPembelian,

            'barang_terlaris' => $barangTerlaris,
            'barang_kurang_laku' => $barangKurangLaku,
            'detail_transaksi_penjualan' => $detailTransaksiPenjualan,
            'detail_transaksi_pembelian' => $detailTransaksiPembelian,
            'ringkasan_perencanaan' => $ringkasanPerencanaan,
            'jumlah_varian_stok_menipis' => $jumlahVarianStokMenipis,
            'estimasi_laba_kotor' => $estimasiLabaKotor,

            'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
            'status' => 'draft',
            'dibuat_oleh' => auth()->id(),
        ]);

        return redirect()->route('laporan-bulanan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat sebagai draft. Periksa dulu sebelum dikirim ke owner.');
    }

    public function show(Laporan $laporan)
    {
        $this->authorize('view', $laporan);

        return view('laporan-bulanan.show', compact('laporan'));
    }

    /*
    |--------------------------------------------------------------------------
    | Download PDF — 3 varian: lengkap, penjualan saja, pembelian saja
    |--------------------------------------------------------------------------
    */

    public function pdf(Laporan $laporan)
    {
        return $this->renderPdf($laporan, 'full');
    }

    public function pdfPenjualan(Laporan $laporan)
    {
        return $this->renderPdf($laporan, 'penjualan');
    }

    public function pdfPembelian(Laporan $laporan)
    {
        return $this->renderPdf($laporan, 'pembelian');
    }

    protected function renderPdf(Laporan $laporan, string $mode)
    {
        $this->authorize('view', $laporan);

        $namaFile = match ($mode) {
            'penjualan' => "{$laporan->kode_laporan}-Penjualan.pdf",
            'pembelian' => "{$laporan->kode_laporan}-Pembelian.pdf",
            default => "{$laporan->kode_laporan}.pdf",
        };

        $pdf = Pdf::loadView('laporan-bulanan.pdf', compact('laporan', 'mode'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($namaFile);
    }

    public function kirim(Laporan $laporan)
    {
        $this->authorize('kirim', $laporan);

        $laporan->update(['status' => 'terkirim', 'dikirim_at' => now()]);

        return back()->with('success', 'Laporan berhasil dikirim ke owner.');
    }

    public function putuskan(Request $request, Laporan $laporan)
    {
        $this->authorize('putuskan', $laporan);

        $validated = $request->validate([
            'keputusan_owner' => 'required|string',
        ]);

        $laporan->update([
            'keputusan_owner' => $validated['keputusan_owner'],
            'status' => 'ditinjau',
            'ditinjau_at' => now(),
        ]);

        return back()->with('success', 'Keputusan berhasil disimpan.');
    }

    protected function formatBarisAgregat($row): array
    {
        return [
            'barang_id'   => $row->barang_id,
            'nama_barang' => $row->nama_barang,
            'kode_barang' => $row->kode_barang,
            'varian_id'   => $row->varian_id,
            'warna'       => $row->warna,
            'ukuran'      => $row->ukuran,
            'sku'         => $row->sku,
            'total_qty'   => (int) $row->total_qty,
            'total_omzet' => (float) $row->total_omzet,
        ];
    }
}