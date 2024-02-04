<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseController;
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
});

Route::get('/laporan/{sale}/show', [SaleController::class, 'showReport'])->name('laporan.show');
Route::get('/laporan/barang/{barang:idBarang}', [SaleController::class, 'laporanBarangBulanan'])->name('laporan.barang.bulanan');

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
Route::get('/barang/data', [ProductController::class, 'data'])->name('barang.data');
Route::controller('ProductController')->group(function () {
    Route::get('/barang', [ProductController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [ProductController::class, 'create'])->name('barang.create');
    Route::post('/barang', [ProductController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang:idBarang}/edit', [ProductController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang:idBarang}', [ProductController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang:idBarang}', [ProductController::class, 'destroy'])->name('barang.destroy');

    Route::get('/barang/cari/data', [ProductController::class, 'searchData'])->name('barang.cari.data');
    Route::get('/barang/kadaluarsa', [ProductController::class, 'expired'])->name('barang.kadaluarsa');
    Route::get('/barang/kadaluarsa/data', [ProductController::class, 'expiredData'])->name('barang.kadaluarsa.data');
    Route::get('/barang/habis', [ProductController::class, 'productEmpty'])->name('barang.habis');
    Route::get('/barang/cetak-harga', [ProductController::class, 'printPrice'])->name('barang.cetak-harga');
    Route::get('/barang/cetak-harga/data', [ProductController::class, 'printPriceData'])->name('barang.cetak-harga.data');
    Route::post('/barang/cetak-harga/store', [ProductController::class, 'storePrintPrice'])->name('barang.cetak-harga.store');
    Route::delete('/barang/cetak-harga/{id}', [ProductController::class, 'destroyPrintPrice'])->name('barang.cetak-harga.destroy');
});

// Kategori
Route::resource('/kategori', CategoryController::class)->except('show');
Route::resource('/merk', MerkController::class)->except('show', 'index');
Route::resource('/supplier', SupplierController::class)->except('show');

// Penjualan

// Belanja
Route::get('/belanja/detail/{purchase}/create', [PurchaseController::class, 'createPurchaseDetails'])->name('belanja.create.purchase-details');
Route::get('/belanja/detail/{purchase}', [PurchaseController::class, 'storePurchaseDetails'])->name('belanja.store.purchase-details');
Route::resource('/belanja', PurchaseController::class);
