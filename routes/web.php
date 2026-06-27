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

    Route::get(
        '/shopee/shop-info',
        [MarketplaceController::class, 'shopInfo']
    );

    Route::get(
        '/shopee/items',
        [MarketplaceController::class, 'getItems']
    );

    Route::get(
        '/shopee/item-info',
        [MarketplaceController::class, 'itemInfo']
    );

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
