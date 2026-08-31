<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailPembelianController;
use App\Http\Controllers\DetailPenjualanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenerimaanPembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PerencanaanPembelianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VarianBarangController;
use App\Http\Controllers\LaporanBulananController;


/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Penjualan
    |--------------------------------------------------------------------------
    |
    | Kasir  : Ya (transaksi penuh)
    | Admin  : Ya
    | Owner  : Tidak
    |
    */

    Route::middleware('role:kasir,admin')->group(function () {

        Route::resource('penjualan', PenjualanController::class);
        Route::resource('detail-penjualan', DetailPenjualanController::class)->only(['index']);
        Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');

    });


    /*
    |--------------------------------------------------------------------------
    | Barang
    |--------------------------------------------------------------------------
    |
    | Kasir  : Ya (lihat saja)
    | Admin  : Ya (full)
    | Owner  : Ya (lihat saja)
    |
    | PENTING: route khusus admin (create, store, edit, update, destroy)
    | HARUS didaftarkan SEBELUM route index/show.
    | Alasan: Route::resource(...)->only(['index','show']) menghasilkan
    | GET /barang/{barang} (untuk show). Karena route itu wildcard,
    | kalau didaftarkan lebih dulu, dia akan "menangkap" duluan request
    | seperti GET /barang/create (create dianggap sebagai {barang}),
    | sehingga route create milik admin tidak pernah kesampaian dan
    | malah menghasilkan 404. Maka urutannya dibalik di bawah ini.
    |
    */

    Route::middleware('role:admin')->group(function () {

        Route::resource('barang', BarangController::class)->except(['index', 'show']);

    });

    Route::middleware('role:kasir,admin,owner')->group(function () {

        Route::resource('barang', BarangController::class)->only(['index', 'show']);

    });


    /*
    |--------------------------------------------------------------------------
    | Perencanaan Pembelian - FULL AKSES untuk Admin & Owner
    |--------------------------------------------------------------------------
    |
    | Kasir  : Tidak
    | Admin  : Ya (full)
    | Owner  : Ya (full — termasuk create, store, cancel, cetak surat)
    |
    */

    Route::middleware('role:admin,owner')->group(function () {

        Route::prefix('perencanaan-pembelian')
            ->name('perencanaan-pembelian.')
            ->group(function () {

                Route::get('/', [PerencanaanPembelianController::class, 'index'])->name('index');
                Route::get('/create', [PerencanaanPembelianController::class, 'create'])->name('create');
                Route::post('/', [PerencanaanPembelianController::class, 'store'])->name('store');
                Route::get('/{perencanaanPembelian}', [PerencanaanPembelianController::class, 'show'])->name('show');
                Route::get('/{id}/struk', [PerencanaanPembelianController::class, 'struk'])->name('struk');
                Route::patch('/{perencanaanPembelian}/cancel', [PerencanaanPembelianController::class, 'cancel'])->name('cancel');

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Laporan - Lihat Saja (Admin & Owner)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:owner')->group(function () {

        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {

                Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
                Route::get('/penjualan/pdf', [LaporanController::class, 'pdfPenjualan'])->name('penjualan.pdf');

                Route::get('/pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
                Route::get('/pembelian/pdf', [LaporanController::class, 'pdfPembelian'])->name('pembelian.pdf');

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Laporan Bulanan - Rekap mandiri Admin & Owner
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,owner')->group(function () {

        Route::prefix('laporan-bulanan')
            ->name('laporan-bulanan.')
            ->group(function () {

                Route::get('/', [LaporanBulananController::class, 'index'])->name('index');
                Route::get('/create', [LaporanBulananController::class, 'create'])->name('create');
                Route::post('/', [LaporanBulananController::class, 'store'])->name('store');
                Route::get('/{laporan}', [LaporanBulananController::class, 'show'])->name('show');
                Route::get('/{laporan}/pdf', [LaporanBulananController::class, 'pdf'])->name('pdf');
                Route::get('/{laporan}/pdf-penjualan', [LaporanBulananController::class, 'pdfPenjualan'])->name('pdf-penjualan');
                Route::get('/{laporan}/pdf-pembelian', [LaporanBulananController::class, 'pdfPembelian'])->name('pdf-pembelian');
                // Route legacy: dipertahankan untuk laporan lama, tidak dipakai flow rekap mandiri.
                Route::patch('/{laporan}/kirim', [LaporanBulananController::class, 'kirim'])->name('kirim');
                Route::patch('/{laporan}/putuskan', [LaporanBulananController::class, 'putuskan'])->name('putuskan');

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Operasional Khusus Admin
    |--------------------------------------------------------------------------
    |
    | Berisi modul yang owner TIDAK boleh akses sama sekali:
    |
    | - Master Data (kategori, varian, supplier)
    | - Pembelian
    | - Penerimaan Pembelian
    | - Marketplace
    |
    | (Barang & Perencanaan Pembelian sudah dipindah ke grup tersendiri di atas)
    |
    */

    Route::middleware('role:admin')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Master Data
        |--------------------------------------------------------------------------
        */

        Route::resource('kategori', KategoriController::class);
        Route::resource('varian', VarianBarangController::class);
        Route::resource('supplier', SupplierController::class);


        /*
        |--------------------------------------------------------------------------
        | Pembelian
        |--------------------------------------------------------------------------
        */

        Route::resource('pembelian', PembelianController::class)->only(['index']);
        Route::resource('detail-pembelian', DetailPembelianController::class)->only(['index']);
        Route::get('/pembelian/{id}/struk', [PembelianController::class, 'struk'])->name('pembelian.struk');


        /*
        |--------------------------------------------------------------------------
        | Penerimaan Pembelian
        |--------------------------------------------------------------------------
        */

        Route::prefix('penerimaan-pembelian')
            ->name('penerimaan-pembelian.')
            ->group(function () {

                Route::get('/', [PenerimaanPembelianController::class, 'index'])->name('index');
                Route::get('/{perencanaanPembelian}/create', [PenerimaanPembelianController::class, 'create'])->name('create');
                Route::post('/{perencanaanPembelian}', [PenerimaanPembelianController::class, 'store'])->name('store');
                Route::get('/detail/{pembelian}', [PenerimaanPembelianController::class, 'show'])->name('show');

            });


        /*
        |--------------------------------------------------------------------------
        | Marketplace
        |--------------------------------------------------------------------------
        */

        Route::prefix('marketplace')
            ->name('marketplace.')
            ->group(function () {

                Route::get('/', [MarketplaceController::class, 'index'])->name('index');
                Route::get('/products', [MarketplaceController::class, 'showProducts'])->name('products');

                Route::get('/mappings', [MarketplaceController::class, 'showMappings'])->name('mappings');
                Route::post('/mappings', [MarketplaceController::class, 'storeMapping'])->name('mappings.store');

                Route::post('/sync/products', [MarketplaceController::class, 'syncProducts'])->name('sync.products');
                Route::post('/sync/variants', [MarketplaceController::class, 'syncVariantsFromShopee'])->name('sync.variants');
                Route::post('/sync/stocks', [MarketplaceController::class, 'syncMarketplaceStockToLocal'])->name('sync.stocks');
                Route::post('/sync/orders', [MarketplaceController::class, 'syncOrdersToLocal'])->name('sync.orders');
                Route::post('/sync/local-stocks', [MarketplaceController::class, 'syncLocalStockToShopee'])->name('sync.local-stocks');

            });


        /*
        |--------------------------------------------------------------------------
        | Shopee Authorization
        |--------------------------------------------------------------------------
        */

        Route::prefix('shopee')
            ->name('shopee.')
            ->group(function () {

                Route::get('/auth', [MarketplaceController::class, 'auth'])->name('auth');
                Route::get('/callback', [MarketplaceController::class, 'callback'])->name('callback');

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
