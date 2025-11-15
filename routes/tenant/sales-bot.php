<?php

use App\Http\Controllers\Tenant\SalesBotController;
use App\Models\Tenant\SalesBot;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant'])->prefix('sales-bot')->name('sales-bot.')->group(function () {
    
    // Main Sales Bot routes
    Route::get('/', [SalesBotController::class, 'index'])->name('index');
    Route::get('/create', [SalesBotController::class, 'create'])->name('create');
    Route::post('/', [SalesBotController::class, 'store'])->name('store');
    
    // Routes with explicit model binding using where clause
    Route::get('/{salesBot}', [SalesBotController::class, 'show'])
        ->name('show')
        ->where('salesBot', '[0-9]+');
    Route::get('/{salesBot}/edit', [SalesBotController::class, 'edit'])
        ->name('edit')
        ->where('salesBot', '[0-9]+');
    Route::put('/{salesBot}', [SalesBotController::class, 'update'])
        ->name('update')
        ->where('salesBot', '[0-9]+');
    
    // Product management
    Route::post('/{salesBot}/sync-products', [SalesBotController::class, 'syncProducts'])
        ->name('sync-products')
        ->where('salesBot', '[0-9]+');
    Route::get('/{salesBot}/products', [SalesBotController::class, 'products'])
        ->name('products')
        ->where('salesBot', '[0-9]+');
    
    // Order management
    Route::get('/{salesBot}/orders', [SalesBotController::class, 'orders'])
        ->name('orders')
        ->where('salesBot', '[0-9]+');
    Route::patch('/{salesBot}/orders/{order}/status', [SalesBotController::class, 'updateOrderStatus'])
        ->name('orders.update-status')
        ->where('salesBot', '[0-9]+')
        ->where('order', '[0-9]+');
    
    // Analytics
    Route::get('/{salesBot}/analytics', [SalesBotController::class, 'analytics'])
        ->name('analytics')
        ->where('salesBot', '[0-9]+');
    
    // Google Sheets testing
    Route::post('/test-connection', [SalesBotController::class, 'testConnection'])->name('test-connection');
});
