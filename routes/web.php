<?php

// routes/web.php

use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentGateways\PaystackController;
use App\Http\Controllers\PaymentGateways\RazorpayController;
use App\Http\Controllers\PaymentGateways\StripeController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermsConditionsController;
use App\Http\Controllers\Whatsapp\WhatsAppWebhookController;
use App\Http\Middleware\SanitizeInputs;
use Corbital\Installer\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landingPage'])->name('home');

// Debug log viewer route
Route::get('/debug-flow-logs', function () {
    $logFile = base_path('botflow_save_debug.log');
    
    if (!file_exists($logFile)) {
        return response('<h1>No Debug Logs Found</h1><p>Log file: botflow_save_debug.log does not exist yet.</p><p>Try saving a flow first to generate logs.</p>');
    }
    
    $logs = file_get_contents($logFile);
    $logs = htmlspecialchars($logs);
    
    return response('<!DOCTYPE html>
<html>
<head>
    <title>Flow Save Debug Logs</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #007cba; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 5px 5px 0 0; }
        .logs { background: #000; color: #00ff00; padding: 15px; border-radius: 5px; white-space: pre-wrap; overflow-x: auto; }
        .clear-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
        .clear-btn:hover { background: #c82333; }
        .refresh-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; margin-left: 10px; }
        .refresh-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Flow Save Debug Logs</h1>
            <p>Live debugging for BotFlow save operations</p>
        </div>
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh Logs</button>
        <button class="clear-btn" onclick="if(confirm(\'Clear all logs?\')) { fetch(\'/clear-debug-logs\', {method:\'POST\'}).then(() => location.reload()); }">🗑️ Clear Logs</button>
        <div class="logs">' . $logs . '</div>
    </div>
    <script>
        // Auto refresh every 10 seconds
        setTimeout(() => location.reload(), 10000);
    </script>
</body>
</html>');
})->name('debug.flow.logs');

// Clear debug logs route
Route::post('/clear-debug-logs', function () {
    $logFile = base_path('botflow_save_debug.log');
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
    }
    return response()->json(['success' => true]);
})->name('clear.debug.logs');

Route::get('/validate', [InstallController::class, 'validate'])->name('validate');
Route::post('/validate', [InstallController::class, 'validateLicense'])->name('validate.license');

// Authentication related routes
require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware(['auth', SanitizeInputs::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// WhatsApp Webhook Route (Supports GET & POST)
Route::match(['get', 'post'], '/whatsapp/webhook', [WhatsAppWebhookController::class, '__invoke'])
    ->name('whatsapp.webhook');

Route::match(['get', 'post'], 'webhooks/stripe', [StripeController::class, 'webhook'])
    ->name('webhook.stripe');

// CSRF Token refresher route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

Route::match(['get', 'post'], 'webhooks/razorpay', [RazorpayController::class, 'webhook'])
    ->name('webhook.razorpay');

// Paystack webhook
Route::match(['get', 'post'], 'webhooks/paystack', [PaystackController::class, 'webhook'])
    ->name('webhook.paystack');

// PayPal routes
Route::match(['get', 'post'], 'webhooks/paypal', [\App\Http\Controllers\PaymentGateways\PayPalController::class, 'handleWebhook'])
    ->name('webhooks.paypal');

Route::get('paypal/subscription/success', [\App\Http\Controllers\PaymentGateways\PayPalController::class, 'subscriptionSuccess'])
    ->name('paypal.subscription.success');

Route::get('paypal/subscription/cancel', [\App\Http\Controllers\PaymentGateways\PayPalController::class, 'subscriptionCancel'])
    ->name('paypal.subscription.cancel');

Route::get('back-to-admin', [AuthenticatedSessionController::class, 'back_to_admin'])->name('back.to.admin');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('privacy.policy');
Route::get('/terms-conditions', [TermsConditionsController::class, 'show'])->name('terms.conditions');

// Theme Style CSS Routes
Route::get('/theme-style-css', [App\Http\Controllers\Admin\ThemeStyleController::class, 'css'])
    ->name('theme-style-css');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/api/translations/{locale?}', [TranslationController::class, 'index'])
    ->name('api.translations');
