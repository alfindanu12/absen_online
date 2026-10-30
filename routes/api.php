<?php

use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\SalesAttendanceController;
use App\Http\Controllers\API\SalesUserSyncController;
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

Route::get('users', [UsersController::class, 'index']);
Route::post('tambah-users', [UsersController::class, 'store']);
Route::post('sales-users/sync', [SalesUserSyncController::class, 'upsert']);
Route::prefix('sales-attendance')->group(function () {
    Route::get('/today', [SalesAttendanceController::class, 'today']);
    Route::post('/check-in', [SalesAttendanceController::class, 'checkIn']);
    Route::post('/check-out', [SalesAttendanceController::class, 'checkOut']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
