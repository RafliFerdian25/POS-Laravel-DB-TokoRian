<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleDetailController;
use App\Models\Product;
use App\Models\ProductSearch;

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
    return redirect()->route('laporan.penjualan');
});

/** LAPORAN */
Route::controller(ReportController::class)->group(function () {
    Route::get('/laporan/penjualan', 'ReportSale')->name('laporan.penjualan');
    Route::get('/laporan/penjualan/data', 'getReportSale')->name('laporan.penjualan.data');
    Route::get('/laporan/penjualan/kategori/data', 'getReportSaleByCategory')->name('report.sale.catgory.data');
    Route::get('/laporan/kategori', 'categoryIndex')->name('laporan.kategori');
    Route::get('/laporan/kategori/data', 'getCategoriesReport')->name('laporan.kategori.data');
    Route::get('/laporan/kategori/{category:ID}/detail', 'categoryDetail')->name('report.category.detail');
    Route::get('/laporan/kategori/{category:ID}/detail/data', 'getCategoryDetail')->name('report.category.detail.data');

    // Laporan Barang
    Route::get('/laporan/barang', 'productReport')->name('monthly.product.report');
    Route::get('/laporan/barang/data', 'getProductReport')->name('monthly.product.report.data');
    Route::get('/laporan/barang/{product:idBarang}', 'productDetail')->name('report.product.detail');
    Route::get('/laporan/barang/{product:idBarang}/data', 'getProductDetail')->name('report.product.detail.data');
});

Route::get('/laporan/{sale}/show', [SaleController::class, 'showReport'])->name('laporan.show');

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
    Route::get('/barang', 'index')->name('barang.index');
    Route::get('/barang/data', 'data')->name('barang.data');
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
Route::controller(CategoryController::class)->group(function () {
    Route::get('/kategori', 'index')->name('category.index');
    Route::get('/kategori/data', 'data')->name('category.data');
    Route::get('/kategori/create', 'create')->name('category.create');
    Route::post('/kategori', 'store')->name('category.store');
    Route::get('/kategori/{category:id}/edit', 'edit')->name('category.edit');
    Route::put('/kategori/{category:id}', 'update')->name('category.update');
    Route::delete('/kategori/{category:id}', 'destroy')->name('category.destroy');
});
Route::resource('/supplier', SupplierController::class)->except('show');

// Penjualan


// Belanja Barang
Route::controller(ShoppingController::class)->group(function () {
    Route::get('/belanja', 'index')->name('wholesale.purchase.index');
    Route::get('/belanja/data', 'indexData')->name('wholesale.purchase.index.data');
    Route::post('/belanja', 'store')->name('wholesale.purchase.store');
    Route::get('/belanja/{product:idBarang}/edit', 'edit')->name('wholesale.purchase.edit');
    Route::put('/belanja/{product:idBarang}', 'update')->name('wholesale.purchase.update');
    Route::delete('/belanja/{product:idBarang}', 'destroy')->name('wholesale.purchase.destroy');
    Route::post('/belanja/upload-data', 'uploadData')->name('wholesale.purchase.upload-data');
    Route::post('/belanja/download-data', 'downloadData')->name('wholesale.purchase.download-data');
});

// Pembelian
Route::controller(PurchaseController::class)->group(function () {
    Route::get('/pembelian', 'index')->name('purchase.index');
    Route::get('/pembelian/data', 'indexData')->name('purchase.index.data');
    Route::get('/pembelian/create', 'create')->name('purchase.create');
    Route::post('/pembelian', 'store')->name('purchase.store');
    Route::get('/pembelian/detail/{purchase:id}/data', 'detailData')->name('purchase.detail.data');
    Route::get('/pembelian/detail/{purchase:id}/create', 'createDetail')->name('purchase.detail.create');
    Route::post('/pembelian/detail/{purchase:id}', 'storeDetail')->name('purchase.detail.store');
    Route::delete('/pembelian/detail/{purchaseDetail:id}', 'destroyDetail')->name('purchase.detail.destroy');
    Route::get('/pembelian/detail/{purchaseDetail:id}/edit', 'editDetail')->name('purchase.detail.edit');
    Route::put('/pembelian/detail/{purchaseDetail:id}', 'updateDetail')->name('purchase.detail.update');
    Route::delete('/pembelian/{purchase:id}', 'destroy')->name('purchase.destroy');
});

// Merk
Route::controller(MerkController::class)->group(function () {
    Route::get('/merk', 'index')->name('merk.index');
    Route::get('/merk/data', 'indexData')->name('merk.data');
    Route::get('/merk/cari/data', 'searchData')->name('merk.search.data');
    Route::get('/merk/create', 'create')->name('merk.create');
    Route::post('/merk', 'store')->name('merk.store');
    Route::get('/merk/{merk:id}/edit', 'edit')->name('merk.edit');
    Route::put('/merk/{merk:id}', 'update')->name('merk.update');
    Route::delete('/merk/{merk:id}', 'destroy')->name('merk.destroy');
});

// Supplier
Route::controller(SupplierController::class)->group(function () {
    Route::get('/supplier', 'index')->name('supplier.index');
    Route::get('/supplier/data', 'indexData')->name('supplier.data');
    Route::get('/supplier/cari/data', 'searchData')->name('supplier.search.data');
    Route::get('/supplier/create', 'create')->name('supplier.create');
    Route::post('/supplier', 'store')->name('supplier.store');
    Route::get('/supplier/{supplier:id}/edit', 'edit')->name('supplier.edit');
    Route::put('/supplier/{supplier:id}', 'update')->name('supplier.update');
    Route::delete('/supplier/{supplier:id}', 'destroy')->name('supplier.destroy');
});

// Barang Dicari
Route::controller(ProductSearchController::class)->group(function () {
    Route::get('/barang-dicari', 'index')->name('product.search.index');
    Route::get('/barang-dicari/data', 'indexData')->name('product.search.index.data');
    Route::post('/barang-dicari', 'store')->name('product.search.store');
    Route::delete('/barang-dicari/{name}', 'destroy')->name('product.search.destroy');
});