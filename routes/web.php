<?php

use App\Http\Controllers\BoxOpenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductHasExpiredBeforeController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CategoryReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GasController;
use App\Http\Controllers\GasCustomerController;
use App\Http\Controllers\GasTransactionController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\ShoppingController;
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
    return redirect()->route('laporan.penjualan');
});

/** LAPORAN */
Route::controller(ReportController::class)->group(function () {
    Route::get('/laporan/penjualan', 'ReportSale')->name('laporan.penjualan');
    Route::get('/laporan/penjualan/data', 'getReportSale')->name('laporan.penjualan.data');
    Route::get('/laporan/penjualan/transaksi-tanggal/data', 'getSaleReportByTransactionDate')->name('sale.report.transaction.date.data');
    Route::get('/laporan/penjualan/barang-terlaris/data', 'getBestSellingProduct')->name('best.selling.product.report.data');
    Route::get('/laporan/penjualan/kategori-terlaris/data', 'getBestSellingCategory')->name('best.selling.category.report.data');
    Route::get('/laporan/penjualan/transaksi-nomer/data', 'getSaleReportTransactionByNoTransaction')->name('sale.report.transaction.data');
    Route::get('/laporan/penjualan/kategori/data', 'getReportSaleByCategory')->name('report.sale.catgory.data');
    Route::get('/laporan/penjualan/detail', 'ReportSaleDetail')->name('report.sale.detail');
    Route::get('/laporan/penjualan/detail/data', 'getReportSaleDetail')->name('laporan.penjualan.detail.data');
});

// Laporan Barang
Route::controller(ProductReportController::class)->group(function () {
    Route::get('/laporan/barang', 'productReport')->name('product.report');
    Route::get('/laporan/barang/data', 'getProductReport')->name('product.report.data');
    Route::get('/laporan/barang/{product:idBarang}', 'productDetail')->name('report.product.detail');
    Route::get('/laporan/barang/{product:idBarang}/data', 'getProductDetail')->name('report.product.detail.data');
});
// Laporan Kategori
Route::controller(CategoryReportController::class)->group(function () {
    Route::get('/laporan/kategori', 'categoryIndex')->name('laporan.kategori');
    Route::get('/laporan/kategori/data', 'getCategoriesReport')->name('laporan.kategori.data');
    Route::get('/laporan/kategori/{category:ID}/detail', 'categoryDetail')->name('report.category.detail');
    Route::get('/laporan/kategori/{category:ID}/detail/data', 'getCategoryDetailReport')->name('report.category.detail.data');
    Route::get('/laporan/kategori/{category:ID}/detail/barang-terlaris/data', 'getBestSellingProductReport')->name('category.detail.best-selling-product.report.data');
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
    Route::get('/supplier/data', 'data')->name('supplier.data');
    Route::get('/supplier/cari/data', 'searchData')->name('supplier.search.data');
    Route::get('/supplier/create', 'create')->name('supplier.create');
    Route::post('/supplier', 'store')->name('supplier.store');
    Route::get('/supplier/{supplier:IdSupplier}/edit', 'edit')->name('supplier.edit');
    Route::put('/supplier/{supplier:IdSupplier}', 'update')->name('supplier.update');
    Route::delete('/supplier/{supplier:IdSupplier}', 'destroy')->name('supplier.destroy');
});

// Barang Dicari
Route::controller(ProductSearchController::class)->group(function () {
    Route::get('/barang-dicari', 'index')->name('product.search.index');
    Route::get('/barang-dicari/data', 'indexData')->name('product.search.index.data');
    Route::post('/barang-dicari', 'store')->name('product.search.store');
    Route::delete('/barang-dicari/{name}', 'destroy')->name('product.search.destroy');
});

// Pengeluaran
Route::controller(ExpenseController::class)->group(function () {
    Route::get('/pengeluaran', 'index')->name('expense.index');
    Route::get('/pengeluaran/data', 'data')->name('expense.data');
    Route::get('/pengeluaran/jumlah/data', 'sumData')->name('expense.sum.data');
    Route::get('/pengeluaran/create', 'create')->name('expense.create');
    Route::put('/pengeluaran/pindah-uang', 'transferMoney')->name('expense.transfer.money');
    Route::post('/pengeluaran', 'store')->name('expense.store');
    Route::get('/pengeluaran/{expense:id}/edit', 'edit')->name('expense.edit');
    Route::put('/pengeluaran/{expense:id}', 'update')->name('expense.update');
    Route::delete('/pengeluaran/{expense:id}', 'destroy')->name('expense.destroy');
});

// Buka Kardus
Route::controller(BoxOpenController::class)->group(function () {
    Route::post('/buka-kardus', 'store')->name('box.open.store');
    Route::get('/buka-kardus/{product:idBarang}/data', 'getProductBoxOpen')->name('box.open.data');
    Route::get('/buka-kardus/{product:idBarang}/create', 'create')->name('box.open.create');
    Route::get('/buka-kardus/{boxOpen:id}/edit', 'edit')->name('box.open.edit');
    Route::put('/buka-kardus/{boxOpen:id}', 'update')->name('box.open.update');
    Route::delete('/buka-kardus/{boxOpen:id}', 'destroy')->name('box.open.destroy');
});

// Barang Pernah Kadaluarsa
Route::controller(ProductHasExpiredBeforeController::class)->group(function () {
    Route::get('/barang-pernah-kadaluarsa', 'index')->name('product.has.expired.before.index');
    Route::get('/barang-pernah-kadaluarsa/data', 'indexData')->name('product.has.expired.before.index.data');
    Route::post('/barang-pernah-kadaluarsa/{product:idBarang}', 'store')->name('product.has.expired.before.store');
    Route::delete('/barang-pernah-kadaluarsa/{productHasExpiredBefore:id}', 'destroy')->name('product.has.expired.before.destroy');
});

// Gas
Route::controller(GasController::class)->group(function () {
    Route::get('/gas', 'index')->name('gas.index');
    Route::get('/gas/data', 'getGases')->name('gas.data');
    Route::post('/gas', 'store')->name('gas.store');
    Route::get('/gas/{gas:id}', 'show')->name('gas.show');
    Route::get('/gas/{gas:id}/tersisa/data', 'getRemainingGas')->name('gas.remaining.data');
    Route::put('/gas/{gas:id}', 'update')->name('gas.update');
    Route::delete('/gas/{gas:id}', 'destroy')->name('gas.destroy');
});

// Pelanggan
Route::controller(CustomerController::class)->group(function () {
    Route::get('/pelanggan', 'index')->name('customer.index');
    Route::get('/pelanggan/data', 'getCustomers')->name('customer.data');
    Route::post('/pelanggan', 'store')->name('customer.store');
    Route::put('/pelanggan/{customer:id}', 'update')->name('customer.update');
    Route::delete('/pelanggan/{customer:id}', 'destroy')->name('customer.destroy');
});

Route::controller(GasCustomerController::class)->group(function () {
    Route::post('/gas-pelanggan', 'store')->name('gas.customer.store');
    Route::get('/gas-pelanggan/{gasID}/data', 'getGasCustomers')->name('gas.customer.data');
    Route::put('/gas-pelanggan/{gasCustomer:id}', 'update')->name('gas.customer.update');
    Route::delete('/gas-pelanggan/{gasCustomer:id}', 'destroy')->name('gas.customer.destroy');
});

Route::controller(GasTransactionController::class)->group(function () {
    Route::post('/gas-transaksi', 'store')->name('gas.transaction.store');
    Route::put('/gas-transaksi/{gasTransaction:id}', 'update')->name('gas.transaction.update');
    Route::get('/gas-transaksi/{gasID}/data', 'getGasTransactions')->name('gas.transaction.data');
    Route::delete('/gas-transaksi/{gasTransaction:id}', 'destroy')->name('gas.transaction.destroy');
});
