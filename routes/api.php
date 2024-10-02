<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:sanctum']);
        Route::get('info', [AuthController::class, 'info']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('register', [AuthController::class, 'register'])->middleware(['roleCheck']);
    });
    Route::apiResource('item', ItemController::class);
    Route::delete('deleteItems', [ItemController::class, 'destroyMany']);
});
