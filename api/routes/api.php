<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\StationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('companies')->group(function () {
    Route::apiResource('companies', CompanyController::class);
});

Route::name('stations')->group(function () {
    Route::get('/stations/search', [StationController::class, 'search']);
    Route::apiResource('stations', StationController::class);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Unhandled route'
    ], 404);
});
