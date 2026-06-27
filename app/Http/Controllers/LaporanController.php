<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        $query = Penjualan::query();

        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween(
                'tanggal_penjualan',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );
        }

        $penjualan = $query
            ->latest()
            ->get();

        $total = $penjualan->sum('total');

        return view(
            'laporan.penjualan',
            compact(
                'penjualan',
                'total'
            )
        );
    }

    public function pembelian(Request $request)
    {
        $query = Pembelian::with('supplier');

        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween(
                'tanggal_pembelian',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );
        }

        $pembelian = $query
            ->latest()
            ->get();

        $total = $pembelian->sum('total_netto');

        return view(
            'laporan.pembelian',
            compact(
                'pembelian',
                'total'
            )
        );
    }

    public function pdfPenjualan(Request $request)
    {
        $query = Penjualan::query();

        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween(
                'tanggal_penjualan',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );
        }

        $penjualan = $query->get();

        $total = $penjualan->sum('total');

        $pdf = Pdf::loadView(
            'laporan.pdf-penjualan',
            compact(
                'penjualan',
                'total'
            )
        );

        return $pdf->download(
            'laporan-penjualan.pdf'
        );
    }

    public function pdfPembelian(Request $request)
    {
        $query = Pembelian::with('supplier');

        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween(
                'tanggal_pembelian',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );
        }

        $pembelian = $query->get();

        $total = $pembelian->sum(
            'total_netto'
        );

        $pdf = Pdf::loadView(
            'laporan.pdf-pembelian',
            compact(
                'pembelian',
                'total'
            )
        );

        return $pdf->download(
            'laporan-pembelian.pdf'
        );
    }
}