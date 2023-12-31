<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DepositController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->middleware(['throttle:5,1'])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::resource('payments', PaymentController::class)->except(['update']);
        Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approve']);
        Route::patch('/payments/{payment}/reject', [PaymentController::class, 'reject']);

        Route::resource('currencies', CurrencyController::class)->only(['index', 'store']);
        Route::patch('/currencies/{currency}/active', [CurrencyController::class, 'active']);
        Route::patch('/currencies/{currency}/deactive', [CurrencyController::class, 'deactive']);

        Route::post('/deposits/transfer', [DepositController::class, 'transfer']);

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth']);
            Route::get('/user-profile', [AuthController::class, 'userProfile'])->middleware(['auth']);
        });
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});
