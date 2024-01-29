<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseController;
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
    return redirect()->route('laporan.bulanan');
});
Route::get('/laporan/{sale}/show', [SaleController::class, 'showReport'])->name('laporan.show');
Route::get('/laporan/keuangan', [SaleController::class, 'laporanBulanan'])->name('laporan.bulanan');
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
    Route::get('/barang/{barang:idBarang}/edit/{type}', [ProductController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang:idBarang}', [ProductController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang:idBarang}', [ProductController::class, 'destroy'])->name('barang.destroy');

    Route::get('/barang/kadaluarsa', [ProductController::class, 'expired'])->name('barang.kadaluarsa');
    Route::get('/barang/kadaluarsa/data', [ProductController::class, 'expiredData'])->name('barang.kadaluarsa.data');
    Route::get('/barang/habis', [ProductController::class, 'productEmpty'])->name('barang.habis');
    Route::get('/barang/cetak-harga', [ProductController::class, 'printPrice'])->name('barang.print-price');
    Route::get('/barang/cetak-harga/data', [ProductController::class, 'printPriceData'])->name('barang.print-price.data');
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
