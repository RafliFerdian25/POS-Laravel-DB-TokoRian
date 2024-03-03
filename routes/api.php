<?php

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

Route::post('/upload-data/merk', [UploadDataController::class, 'merk']);
Route::post('/upload-data/category', [UploadDataController::class, 'category']);
Route::post('/upload-data/unit', [UploadDataController::class, 'unit']);
Route::post('/upload-data/product', [UploadDataController::class, 'product']);
Route::post('/upload-data/search-product', [UploadDataController::class, 'searchProduct']);
Route::post('/upload-data/shopping', [UploadDataController::class, 'shopping']);
Route::post('/upload-data/sale', [UploadDataController::class, 'sale']);
Route::post('/upload-data/purchase', [UploadDataController::class, 'purchase']);
Route::post('/upload-data/purchase-detail', [UploadDataController::class, 'purchase-detail']);
Route::post('/upload-data/receivable', [UploadDataController::class, 'receivable']);
Route::post('/upload-data/supplier', [UploadDataController::class, 'supplier']);
