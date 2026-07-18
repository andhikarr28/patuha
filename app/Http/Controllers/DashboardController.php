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
        /*
        |--------------------------------------------------------------------------
        | PERIODE DASHBOARD
        |--------------------------------------------------------------------------
        |
        | Dashboard transaksi menggunakan bulan dan tahun berjalan.
        |
        */

        $bulan = now()->month;
        $tahun = now()->year;


        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        $totalBarang = Barang::count();

        $totalVarian = VarianBarang::count();

        $totalSupplier = Supplier::count();

        $totalStok = VarianBarang::sum('stok');


        /*
        |--------------------------------------------------------------------------
        | TOTAL PENJUALAN BULAN INI
        |--------------------------------------------------------------------------
        */

        $totalPenjualan = Penjualan::whereMonth(
                'tanggal_penjualan',
                $bulan
            )
            ->whereYear(
                'tanggal_penjualan',
                $tahun
            )
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBELIAN BULAN INI
        |--------------------------------------------------------------------------
        |
        | Pembelian merupakan transaksi yang terbentuk dari proses
        | penerimaan barang.
        |
        | Jadi perencanaan pembelian TIDAK dihitung sebagai pembelian.
        |
        */

        $totalPembelian = Pembelian::whereMonth(
                'tanggal_pembelian',
                $bulan
            )
            ->whereYear(
                'tanggal_pembelian',
                $tahun
            )
            ->sum('total_netto');


        /*
        |--------------------------------------------------------------------------
        | STOK MENIPIS
        |--------------------------------------------------------------------------
        |
        | Varian dianggap menipis apabila:
        |
        | stok <= stok_minimum
        |
        */

        $stokMenipis = VarianBarang::with('barang')
            ->whereColumn(
                'stok',
                '<=',
                'stok_minimum'
            )
            ->orderBy('stok')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | MENUNGGU PENERIMAAN
        |--------------------------------------------------------------------------
        |
        | Jangan hanya melihat status.
        |
        | Sebuah perencanaan masih menunggu penerimaan apabila masih
        | memiliki detail dengan:
        |
        | qty_diterima < qty_rencana
        |
        | Dengan cara ini:
        |
        | 10 direncanakan
        |  0 diterima  -> masih menunggu
        |
        | 10 direncanakan
        |  5 diterima  -> masih menunggu
        |
        | 10 direncanakan
        | 10 diterima  -> selesai
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | TOP 5 BARANG TERLARIS BULAN INI
        |--------------------------------------------------------------------------
        |
        | Menggabungkan:
        |
        | detail_penjualan
        |      ↓
        | varian_barang
        |      ↓
        | barang
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | PENJUALAN PER CHANNEL BULAN INI
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | offline
        | shopee
        | tokopedia
        | tiktok
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE DASHBOARD
        |--------------------------------------------------------------------------
        */

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