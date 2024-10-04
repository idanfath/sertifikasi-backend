<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:sanctum']);
        Route::get('info', [AuthController::class, 'info']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('register', [AuthController::class, 'register'])->middleware(['roleCheck']);
    });

    Route::prefix('admin')->middleware(['roleCheck'])->group(function () {
        Route::apiResource('user', adminController::class);
        Route::delete('deleteUsers', [adminController::class, 'destroyMany']);
    });

    Route::apiResource('item', ItemController::class);
    Route::get('skucheck/{sku}', [ItemController::class, 'skuCheck']);
    Route::delete('deleteItems', [ItemController::class, 'destroyMany']);
    Route::apiResource('transaction', TransactionController::class);
    Route::delete('deleteTransactions', [TransactionController::class, 'destroyMany']);
    Route::apiResource('user', UserController::class)->only(['update']);
});
