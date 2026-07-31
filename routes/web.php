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
    |
    | Semua role boleh membuka dashboard.
    |
    */

    Route::get(
        '/dashboard',
        [
            DashboardController::class,
            'index'
        ]
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Penjualan
    |--------------------------------------------------------------------------
    |
    | Kasir  : Ya
    | Admin  : Ya
    | Owner  : Ya
    |
    */

    Route::middleware(
        'role:kasir,admin,owner'
    )->group(function () {

        Route::resource(
            'penjualan',
            PenjualanController::class
        );

        Route::resource(
            'detail-penjualan',
            DetailPenjualanController::class
        );
        Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');


    });


    /*
    |--------------------------------------------------------------------------
    | Operasional Admin & Owner
    |--------------------------------------------------------------------------
    |
    | Berisi:
    |
    | - Master Data
    | - Pembelian
    | - Perencanaan Pembelian
    | - Penerimaan Pembelian
    | - Marketplace
    | - Laporan
    |
    */

    Route::middleware(
        'role:admin,owner'
    )->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Master Data
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'kategori',
            KategoriController::class
        );

        Route::resource(
            'barang',
            BarangController::class
        );

        Route::resource(
            'varian',
            VarianBarangController::class
        );

        Route::resource(
            'supplier',
            SupplierController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Pembelian
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'pembelian',
            PembelianController::class
        );

        Route::resource(
            'detail-pembelian',
            DetailPembelianController::class
        );
        Route::get('/pembelian/{id}/struk', [PembelianController::class, 'struk'])->name('pembelian.struk');


        /*
        |--------------------------------------------------------------------------
        | Perencanaan Pembelian
        |--------------------------------------------------------------------------
        */

        Route::prefix('perencanaan-pembelian')
            ->name('perencanaan-pembelian.')
            ->group(function () {

                Route::get(
                    '/',
                    [
                        PerencanaanPembelianController::class,
                        'index'
                    ]
                )->name('index');


                Route::get(
                    '/create',
                    [
                        PerencanaanPembelianController::class,
                        'create'
                    ]
                )->name('create');


                Route::post(
                    '/',
                    [
                        PerencanaanPembelianController::class,
                        'store'
                    ]
                )->name('store');


                Route::get(
                    '/{perencanaanPembelian}',
                    [
                        PerencanaanPembelianController::class,
                        'show'
                    ]
                )->name('show');


                Route::patch(
                    '/{perencanaanPembelian}/cancel',
                    [
                        PerencanaanPembelianController::class,
                        'cancel'
                    ]
                )->name('cancel');

            });

            Route::get('/{id}/struk', [PerencanaanPembelianController::class, 'struk'])->name('perencanaan-pembelian.struk');


        /*
        |--------------------------------------------------------------------------
        | Penerimaan Pembelian
        |--------------------------------------------------------------------------
        */

        Route::prefix('penerimaan-pembelian')
            ->name('penerimaan-pembelian.')
            ->group(function () {

                Route::get(
                    '/',
                    [
                        PenerimaanPembelianController::class,
                        'index'
                    ]
                )->name('index');


                Route::get(
                    '/{perencanaanPembelian}/create',
                    [
                        PenerimaanPembelianController::class,
                        'create'
                    ]
                )->name('create');


                Route::post(
                    '/{perencanaanPembelian}',
                    [
                        PenerimaanPembelianController::class,
                        'store'
                    ]
                )->name('store');


                Route::get(
                    '/detail/{pembelian}',
                    [
                        PenerimaanPembelianController::class,
                        'show'
                    ]
                )->name('show');

            });


        /*
        |--------------------------------------------------------------------------
        | Laporan
        |--------------------------------------------------------------------------
        */

        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {

                /*
                | Laporan Penjualan
                */

                Route::get(
                    '/penjualan',
                    [
                        LaporanController::class,
                        'penjualan'
                    ]
                )->name('penjualan');


                Route::get(
                    '/penjualan/pdf',
                    [
                        LaporanController::class,
                        'pdfPenjualan'
                    ]
                )->name('penjualan.pdf');


                /*
                | Laporan Pembelian
                */

                Route::get(
                    '/pembelian',
                    [
                        LaporanController::class,
                        'pembelian'
                    ]
                )->name('pembelian');


                Route::get(
                    '/pembelian/pdf',
                    [
                        LaporanController::class,
                        'pdfPembelian'
                    ]
                )->name('pembelian.pdf');

            });


        /*
        |--------------------------------------------------------------------------
        | Marketplace
        |--------------------------------------------------------------------------
        */

        Route::prefix('marketplace')
            ->name('marketplace.')
            ->group(function () {

                /*
                | Dashboard Marketplace
                */

                Route::get(
                    '/',
                    [
                        MarketplaceController::class,
                        'index'
                    ]
                )->name('index');


                /*
                | Produk Shopee
                */

                Route::get(
                    '/products',
                    [
                        MarketplaceController::class,
                        'showProducts'
                    ]
                )->name('products');


                /*
                | Mapping SKU
                */

                Route::get(
                    '/mappings',
                    [
                        MarketplaceController::class,
                        'showMappings'
                    ]
                )->name('mappings');


                Route::post(
                    '/mappings',
                    [
                        MarketplaceController::class,
                        'storeMapping'
                    ]
                )->name('mappings.store');


                /*
                | Sinkron Produk Shopee
                */

                Route::post(
                    '/sync/products',
                    [
                        MarketplaceController::class,
                        'syncProducts'
                    ]
                )->name('sync.products');


                /*
                | Sinkron Variasi Shopee
                */

                Route::post(
                    '/sync/variants',
                    [
                        MarketplaceController::class,
                        'syncVariantsFromShopee'
                    ]
                )->name('sync.variants');


                /*
                | Shopee -> Lokal
                */

                Route::post(
                    '/sync/stocks',
                    [
                        MarketplaceController::class,
                        'syncMarketplaceStockToLocal'
                    ]
                )->name('sync.stocks');


                /*
                | Sinkron Order Shopee
                */

                Route::post(
                    '/sync/orders',
                    [
                        MarketplaceController::class,
                        'syncOrdersToLocal'
                    ]
                )->name('sync.orders');


                /*
                | Lokal -> Shopee
                */

                Route::post(
                    '/sync/local-stocks',
                    [
                        MarketplaceController::class,
                        'syncLocalStockToShopee'
                    ]
                )->name('sync.local-stocks');

            });


        /*
        |--------------------------------------------------------------------------
        | Shopee Authorization
        |--------------------------------------------------------------------------
        |
        | Auth:
        | User diarahkan ke Shopee untuk memberikan izin.
        |
        | Callback:
        | Shopee otomatis mengembalikan user ke endpoint ini.
        |
        */

        Route::prefix('shopee')
            ->name('shopee.')
            ->group(function () {

                Route::get(
                    '/auth',
                    [
                        MarketplaceController::class,
                        'auth'
                    ]
                )->name('auth');


                Route::get(
                    '/callback',
                    [
                        MarketplaceController::class,
                        'callback'
                    ]
                )->name('callback');

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    |
    | Semua user yang login boleh mengelola profil sendiri.
    |
    */

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit'
        ]
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update'
        ]
    )->name('profile.update');


    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy'
        ]
    )->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';