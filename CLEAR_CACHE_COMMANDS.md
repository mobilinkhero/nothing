# 🚀 **Clear Laravel Cache Commands**

The route model binding error might be due to cached routes. Run these commands to clear all cache:

## **🧹 Clear All Cache:**
```bash
# Clear route cache (IMPORTANT for route model binding)
php artisan route:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Clear application cache
php artisan cache:clear

# Clear compiled classes
php artisan clear-compiled

# Optimize for production (regenerate caches)
php artisan optimize
```

## **🔧 For Development:**
```bash
# Clear everything at once
php artisan optimize:clear
```

## **📋 Specific for Route Model Binding Issues:**
```bash
# Most important - clear route cache
php artisan route:clear

# Then regenerate routes
php artisan route:cache
```

## **🚨 If Still Not Working:**

### **Option 1: Manual Route Model Binding (Fallback)**
If automatic binding still fails, I can modify the controller methods to manually resolve the model:

```php
public function syncProducts($salesBotId): JsonResponse
{
    $salesBot = SalesBot::where('id', $salesBotId)
        ->where('tenant_id', current_tenant()->id)
        ->firstOrFail();
    
    // Rest of method...
}
```

### **Option 2: Use Route Resource**
Change to Laravel resource routes which handle model binding better:

```php
Route::resource('sales-bot', SalesBotController::class);
```

### **Option 3: Explicit Binding in RouteServiceProvider**
Add explicit binding in `app/Providers/RouteServiceProvider.php`:

```php
public function boot()
{
    Route::bind('salesBot', function ($value) {
        return SalesBot::where('id', $value)
            ->where('tenant_id', current_tenant()->id)
            ->firstOrFail();
    });
}
```

## **🎯 Try This Order:**
1. **Clear route cache**: `php artisan route:clear`
2. **Clear all cache**: `php artisan optimize:clear`  
3. **Test the sync products button**
4. **If still failing**: Use manual binding fallback

The route model binding should work after clearing the cache! 🚀
