<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VarianBarangController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\DetailPembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DetailPenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PerencanaanPembelianController;
use App\Http\Controllers\PenerimaanPembelianController;



use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('kategori', KategoriController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('varian', VarianBarangController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('pembelian', PembelianController::class);
    Route::resource(
        'detail-pembelian',
        DetailPembelianController::class
    );
    Route::resource(
        'penjualan',
        PenjualanController::class
    );
    Route::resource(
        'detail-penjualan',
        DetailPenjualanController::class
    );

    Route::get(
        '/laporan/penjualan',
        [LaporanController::class, 'penjualan']
    )->name('laporan.penjualan');

    Route::get(
        '/laporan/pembelian',
        [LaporanController::class, 'pembelian']
    )->name('laporan.pembelian');

    Route::get(
        '/laporan/penjualan/pdf',
        [LaporanController::class, 'pdfPenjualan']
    )->name('laporan.penjualan.pdf');

    Route::get(
        '/laporan/pembelian/pdf',
        [LaporanController::class, 'pdfPembelian']
    )->name('laporan.pembelian.pdf');

    Route::get(
        '/marketplace',
        [MarketplaceController::class, 'index']
    )->name('marketplace.index');

    Route::get(
        '/shopee/auth',
        [MarketplaceController::class, 'auth']
    )->name('shopee.auth');

    Route::get(
        '/shopee/callback',
        [MarketplaceController::class, 'callback']
    )->name('shopee.callback');

    Route::prefix('perencanaan-pembelian')
        ->name('perencanaan-pembelian.')
        ->group(function () {

            Route::get(
                '/',
                [PerencanaanPembelianController::class, 'index']
            )->name('index');

            Route::get(
                '/create',
                [PerencanaanPembelianController::class, 'create']
            )->name('create');

            Route::post(
                '/',
                [PerencanaanPembelianController::class, 'store']
            )->name('store');

            Route::get(
                '/{perencanaanPembelian}',
                [PerencanaanPembelianController::class, 'show']
            )->name('show');

            Route::patch(
                '/{perencanaanPembelian}/cancel',
                [PerencanaanPembelianController::class, 'cancel']
            )->name('cancel');
        });

    Route::prefix('penerimaan-pembelian')
        ->name('penerimaan-pembelian.')
        ->group(function () {

            Route::get(
                '/',
                [PenerimaanPembelianController::class, 'index']
            )->name('index');

            Route::get(
                '/{perencanaanPembelian}/create',
                [PenerimaanPembelianController::class, 'create']
            )->name('create');

            Route::post(
                '/{perencanaanPembelian}',
                [PenerimaanPembelianController::class, 'store']
            )->name('store');

            Route::get(
                '/detail/{pembelian}',
                [PenerimaanPembelianController::class, 'show']
            )->name('show');

        });


    Route::prefix('marketplace')->group(function () {

        Route::get(
            '/products',
            [MarketplaceController::class, 'showProducts']
        )->name('marketplace.products');

        Route::get(
            '/mappings',
            [MarketplaceController::class, 'showMappings']
        )->name('marketplace.mappings');

        Route::post(
            '/mappings',
            [MarketplaceController::class, 'storeMapping']
        )->name('marketplace.mappings.store');

        Route::post(
            '/sync/products',
            [MarketplaceController::class, 'syncProducts']
        )->name('marketplace.sync.products');

        Route::post(
            '/sync/variants',
            [MarketplaceController::class, 'syncVariantsFromShopee']
        )->name('marketplace.sync.variants');

        Route::post(
            '/sync/stocks',
            [MarketplaceController::class, 'syncMarketplaceStockToLocal']
        )->name('marketplace.sync.stocks');

        Route::post(
            '/sync/orders',
            [MarketplaceController::class, 'syncOrdersToLocal']
        )->name('marketplace.sync.orders');

        Route::post(
            '/sync/local-stocks',
            [MarketplaceController::class, 'syncLocalStockToShopee']
        )->name('marketplace.sync.local-stocks');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
