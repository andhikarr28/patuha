<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\VarianBarang;
use App\Models\PerencanaanPembelian;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        $totalBarang = Barang::count();

        $totalVarian = VarianBarang::count();

        $totalSupplier = Supplier::count();

        $totalStok = VarianBarang::sum('stok');

        $totalPenjualan = Penjualan::whereMonth(
                'tanggal_penjualan',
                $bulan
            )
            ->whereYear(
                'tanggal_penjualan',
                $tahun
            )
            ->sum('total');


        $totalPembelian = Pembelian::whereMonth(
                'tanggal_pembelian',
                $bulan
            )
            ->whereYear(
                'tanggal_pembelian',
                $tahun
            )
            ->sum('total_netto');


        $stokMenipis = VarianBarang::with('barang')
            ->whereColumn(
                'stok',
                '<=',
                'stok_minimum'
            )
            ->orderBy('stok')
            ->get();

        $menungguPenerimaan = PerencanaanPembelian::where(
                'status',
                '!=',
                'dibatalkan'
            )
            ->whereHas(
                'details',
                function ($query) {

                    $query->whereColumn(
                        'qty_diterima',
                        '<',
                        'qty_rencana'
                    );

                }
            )
            ->count();

        $barangTerlaris = DB::table('detail_penjualan')

            ->join(
                'penjualan',
                'detail_penjualan.penjualan_id',
                '=',
                'penjualan.id'
            )

            ->join(
                'varian_barang',
                'detail_penjualan.varian_id',
                '=',
                'varian_barang.id'
            )

            ->join(
                'barang',
                'varian_barang.barang_id',
                '=',
                'barang.id'
            )

            ->whereMonth(
                'penjualan.tanggal_penjualan',
                $bulan
            )

            ->whereYear(
                'penjualan.tanggal_penjualan',
                $tahun
            )

            ->select(
                'barang.id',
                'barang.nama_barang',

                DB::raw(
                    'SUM(detail_penjualan.qty)
                    as total_terjual'
                )
            )

            ->groupBy(
                'barang.id',
                'barang.nama_barang'
            )

            ->orderByDesc(
                'total_terjual'
            )

            ->limit(5)

            ->get();

        $penjualanChannel = Penjualan::select(
                'channel',

                DB::raw(
                    'SUM(total) as total'
                )
            )

            ->whereMonth(
                'tanggal_penjualan',
                $bulan
            )

            ->whereYear(
                'tanggal_penjualan',
                $tahun
            )

            ->groupBy(
                'channel'
            )

            ->orderByDesc(
                'total'
            )

            ->get();


        return view(
            'dashboard.index',
            compact(

                'totalBarang',

                'totalVarian',

                'totalSupplier',

                'totalStok',

                'totalPenjualan',

                'totalPembelian',

                'stokMenipis',

                'menungguPenerimaan',

                'barangTerlaris',

                'penjualanChannel'

            )
        );
    }
}