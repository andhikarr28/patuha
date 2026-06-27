<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\VarianBarang;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();

        $totalVarian = VarianBarang::count();

        $totalSupplier = Supplier::count();

        $totalStok = VarianBarang::sum('stok');

        $totalPenjualan = Penjualan::sum('total');

        $totalPembelian = Pembelian::sum('total_netto');

        $stokMenipis = VarianBarang::with('barang')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->get();

        $barangTerlaris = DB::table('detail_penjualan')
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
            ->select(
                'barang.nama_barang',
                DB::raw('SUM(detail_penjualan.qty) as total_terjual')
            )
            ->groupBy('barang.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $penjualanChannel = Penjualan::select(
                'channel',
                DB::raw('SUM(total) as total')
            )
            ->groupBy('channel')
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
                'barangTerlaris',
                'penjualanChannel'
            )
        );
    }
}