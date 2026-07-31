<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{

    public function penjualan(Request $request)
    {
        /*
        | Jika user belum memilih tanggal,
        | default = awal sampai akhir bulan berjalan.
        */

        $tanggalAwal = $request->tanggal_awal
            ?? now()->startOfMonth()->toDateString();

        $tanggalAkhir = $request->tanggal_akhir
            ?? now()->endOfMonth()->toDateString();

        $penjualan = Penjualan::query()
            ->whereBetween(
                'tanggal_penjualan',
                [
                    $tanggalAwal,
                    $tanggalAkhir
                ]
            )
            ->latest('tanggal_penjualan')
            ->latest('id')
            ->get();

        $total = $penjualan->sum('total');

        $jumlahTransaksi = $penjualan->count();

        $rataRata = $jumlahTransaksi > 0
            ? $total / $jumlahTransaksi
            : 0;

        return view(
            'laporan.penjualan',
            compact(
                'penjualan',
                'total',
                'jumlahTransaksi',
                'rataRata',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );
    }


    public function pembelian(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal
            ?? now()->startOfMonth()->toDateString();

        $tanggalAkhir = $request->tanggal_akhir
            ?? now()->endOfMonth()->toDateString();

        $pembelian = Pembelian::with('supplier')
            ->whereBetween(
                'tanggal_pembelian',
                [
                    $tanggalAwal,
                    $tanggalAkhir
                ]
            )
            ->latest('tanggal_pembelian')
            ->latest('id')
            ->get();

        $total = $pembelian->sum('total_netto');

        $jumlahTransaksi = $pembelian->count();

        $rataRata = $jumlahTransaksi > 0
            ? $total / $jumlahTransaksi
            : 0;

        return view(
            'laporan.pembelian',
            compact(
                'pembelian',
                'total',
                'jumlahTransaksi',
                'rataRata',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );
    }


    public function pdfPenjualan(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal
            ?? now()->startOfMonth()->toDateString();

        $tanggalAkhir = $request->tanggal_akhir
            ?? now()->endOfMonth()->toDateString();

        $penjualan = Penjualan::whereBetween(
                'tanggal_penjualan',
                [
                    $tanggalAwal,
                    $tanggalAkhir
                ]
            )
            ->latest('tanggal_penjualan')
            ->get();

        $total = $penjualan->sum('total');

        $pdf = Pdf::loadView(
            'laporan.pdf-penjualan',
            compact(
                'penjualan',
                'total',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );

        return $pdf->stream(
            'laporan-penjualan-' .
            $tanggalAwal .
            '-sd-' .
            $tanggalAkhir .
            '.pdf'
        );
    }


    public function pdfPembelian(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal
            ?? now()->startOfMonth()->toDateString();

        $tanggalAkhir = $request->tanggal_akhir
            ?? now()->endOfMonth()->toDateString();

        $pembelian = Pembelian::with('supplier')
            ->whereBetween(
                'tanggal_pembelian',
                [
                    $tanggalAwal,
                    $tanggalAkhir
                ]
            )
            ->latest('tanggal_pembelian')
            ->get();

        $total = $pembelian->sum('total_netto');

        $pdf = Pdf::loadView(
            'laporan.pdf-pembelian',
            compact(
                'pembelian',
                'total',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );

        return $pdf->stream(
            'laporan-pembelian-' .
            $tanggalAwal .
            '-sd-' .
            $tanggalAkhir .
            '.pdf'
        );
    }
}