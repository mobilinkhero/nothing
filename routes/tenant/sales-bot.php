<?php

use App\Http\Controllers\Tenant\SalesBotController;
use App\Models\Tenant\SalesBot;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant'])->prefix('sales-bot')->name('sales-bot.')->group(function () {
    
    // Bind the salesBot parameter to the SalesBot model
    Route::bind('salesBot', function ($value) {
        $tenantId = current_tenant()?->id ?? request()->route('subdomain');
        
        if (!$tenantId && function_exists('tenant_id')) {
            $tenantId = tenant_id();
        }
        
        return SalesBot::where('id', $value)
            ->when($tenantId, function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            })
            ->firstOrFail();
    });
    
    // Main Sales Bot routes
    Route::get('/', [SalesBotController::class, 'index'])->name('index');
    Route::get('/create', [SalesBotController::class, 'create'])->name('create');
    Route::post('/', [SalesBotController::class, 'store'])->name('store');
    Route::get('/{salesBot}', [SalesBotController::class, 'show'])->name('show');
    Route::get('/{salesBot}/edit', [SalesBotController::class, 'edit'])->name('edit');
    Route::put('/{salesBot}', [SalesBotController::class, 'update'])->name('update');
    
    // Product management
    Route::post('/{salesBot}/sync-products', [SalesBotController::class, 'syncProducts'])->name('sync-products');
    Route::get('/{salesBot}/products', [SalesBotController::class, 'products'])->name('products');
    
    // Order management
    Route::get('/{salesBot}/orders', [SalesBotController::class, 'orders'])->name('orders');
    Route::patch('/{salesBot}/orders/{order}/status', [SalesBotController::class, 'updateOrderStatus'])->name('orders.update-status');
    
    // Analytics
    Route::get('/{salesBot}/analytics', [SalesBotController::class, 'analytics'])->name('analytics');
    
    // Google Sheets testing
    Route::post('/test-connection', [SalesBotController::class, 'testConnection'])->name('test-connection');
});
