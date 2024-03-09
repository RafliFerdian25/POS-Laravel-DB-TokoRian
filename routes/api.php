<?php

use App\Http\Controllers\DownloadDataController;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\UploadData;
use App\Http\Controllers\UploadDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UploadDataController::class)->group(function () {
    Route::post('/upload-data/merk', 'merk');
    Route::post('/upload-data/category', 'category');
    Route::post('/upload-data/unit', 'unit');
    Route::post('/upload-data/product', 'product');
    Route::post('/upload-data/search-product', 'searchProduct');
    Route::post('/upload-data/shopping', 'shopping');
    Route::post('/upload-data/sale', 'sale');
    Route::post('/upload-data/purchase', 'purchase');
    Route::post('/upload-data/purchase-detail', 'purchaseDetail');
    Route::post('/upload-data/receivable', 'receivable');
    Route::post('/upload-data/supplier', 'supplier');
});

Route::controller(DownloadDataController::class)->group(function () {
    // Route::get('/download-data/merk', 'merk');
    // Route::get('/download-data/category', 'category');
    // Route::get('/download-data/unit', 'unit');
    // Route::get('/download-data/product', 'product');
    Route::get('/download-data/shopping', 'shopping');
    // Route::get('/download-data/sale', 'sale');
    // Route::get('/download-data/purchase', 'purchase');
    // Route::get('/download-data/purchase-detail', 'purchaseDetail');
    // Route::get('/download-data/receivable', 'receivable');
    // Route::get('/download-data/supplier', 'supplier');
});


// Belanja Barang
Route::controller(ShoppingController::class)->group(function () {
    Route::post('/belanja', 'store')->name('wholesale.purchase.store');
    Route::post('/belanja/upload-data', 'uploadData')->name('wholesale.purchase.upload-data');
    Route::put('/belanja/{shopping:id}', 'update')->name('wholesale.purchase.update');
    Route::delete('/belanja/{shopping:id}', 'destroy')->name('wholesale.purchase.destroy');
});
