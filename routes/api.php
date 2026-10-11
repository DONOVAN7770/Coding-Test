<?php

use App\Http\Controllers\Admin\OrderSubmissionController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('products.show');
    Route::post('/orders', OrderSubmissionController::class)
        ->middleware('throttle:30,1')
        ->name('orders.store');
});
