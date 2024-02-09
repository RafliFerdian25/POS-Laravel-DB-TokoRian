<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WholesalePurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleDetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Laporan */

Route::get('/', function () {
    return redirect()->route('laporan.penjualan.bulanan');
});

/** LAPORAN */
Route::controller(ReportController::class)->group(function () {
    Route::get('/laporan/kategori', 'categoryIndex')->name('laporan.kategori');
    Route::get('/laporan/kategori/data', 'getCategoriesReport')->name('laporan.kategori.data');
    Route::get('/laporan/penjualan/bulanan', 'ReportSaleMonthly')->name('laporan.penjualan.bulanan');
    Route::get('/laporan/barang', 'monthlyProductReport')->name('monthly.product.report');
    Route::get('/laporan/barang/data', 'monthlyProductReportData')->name('monthly.product.report.data');
});

Route::get('/laporan/{sale}/show', [SaleController::class, 'showReport'])->name('laporan.show');
Route::get('/laporan/barang/{product:idBarang}', [SaleController::class, 'laporanBarangBulanan'])->name('laporan.barang.bulanan');

// Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
Route::get('/transaksi/kasir', [SaleDetailController::class, 'index'])->name('penjualan.index');
Route::post('/transaksi/simpan', [SaleController::class, 'store'])->name('transaksi.simpan');
Route::get('/transaksi/selesai', [SaleController::class, 'selesai'])->name('transaksi.selesai');
Route::get('/transaksi/nota-kecil', [SaleController::class, 'notaKecil'])->name('transaksi.nota_kecil');
Route::get('/transaksi/{id}/data', [SaleDetailController::class, 'data'])->name('transaksi.data');
Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [SaleDetailController::class, 'loadForm'])->name('transaksi.load_form');
Route::resource('/transaksi', SaleDetailController::class)
    ->except('create', 'show', 'edit');

// Barang
Route::controller(ProductController::class)->group(function () {
    Route::get('/barang/data', 'data')->name('barang.data');
    Route::get('/barang', 'index')->name('barang.index');
    Route::get('/barang/create', 'create')->name('barang.create');
    Route::post('/barang', 'store')->name('barang.store');
    Route::get('/barang/{product:idBarang}/edit', 'edit')->name('barang.edit');
    Route::put('/barang/{product:idBarang}', 'update')->name('barang.update');
    Route::delete('/barang/{product:idBarang}', 'destroy')->name('barang.destroy');
    Route::get('/barang/cari/data', 'searchData')->name('barang.cari.data');

    // Kadaluarsa
    Route::get('/barang/kadaluarsa', 'expired')->name('barang.kadaluarsa');
    Route::get('/barang/kadaluarsa/data', 'expiredData')->name('barang.kadaluarsa.data');

    // Habis
    Route::get('/barang/habis', 'empty')->name('barang.habis');
    Route::get('/barang/habis/data', 'emptyData')->name('barang.habis.data');

    // Cetak Harga
    Route::get('/barang/cetak-harga', 'printPrice')->name('barang.cetak-harga');
    Route::get('/barang/cetak-harga/data', 'printPriceData')->name('barang.cetak-harga.data');
    Route::put('/barang/cetak-harga/{product:idBarang}', 'updateFromPrintPrice')->name('product.print.price.update');
    Route::post('/barang/cetak-harga/store', 'storePrintPrice')->name('barang.cetak-harga.store');
    Route::delete('/barang/cetak-harga/{id}', 'destroyPrintPrice')->name('barang.cetak-harga.destroy');
});

// Kategori
Route::resource('/kategori', CategoryController::class)->except('show');
Route::resource('/merk', MerkController::class)->except('show', 'index');
Route::resource('/supplier', SupplierController::class)->except('show');

// Penjualan

// Belanja
Route::get('/belanja/detail/{purchase}/create', [WholesalePurchaseController::class, 'createPurchaseDetails'])->name('belanja.create.purchase-details');
Route::get('/belanja/detail/{purchase}', [WholesalePurchaseController::class, 'storePurchaseDetails'])->name('belanja.store.purchase-details');

// Belanja Barang
Route::controller(WholesalePurchaseController::class)->group(function () {
    Route::get('/belanja', 'index')->name('wholesale.purchase.index');
    Route::get('/belanja/data', 'indexData')->name('wholesale.purchase.index.data');
    Route::post('/belanja', 'store')->name('wholesale.purchase.store');
    Route::get('/belanja/{wholesalePurchase:id}/edit', 'edit')->name('wholesale.purchase.edit');
    Route::put('/belanja/{wholesalePurchase:id}', 'update')->name('wholesale.purchase.update');
    Route::delete('/belanja/{wholesalePurchase:id}', 'destroy')->name('wholesale.purchase.destroy');
});
