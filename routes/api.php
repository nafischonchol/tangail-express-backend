<?php

use App\Http\Controllers\Customer\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return responseSuccess(['status' => 'healthy'], 'Tangail Express API is healthy');
});

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
