<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\WhatsAppWebhookController;

use App\Models\Customer;
use App\Models\Message;
use App\Models\WebhookLog;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/shopify/order-created', [ShopifyController::class, 'orderCreated']);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::post('/whatsapp/test', [WhatsAppController::class, 'test']);

Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);

Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'receive']);

Route::get('/debug/environment', function () {
    return [
        'app_env' => app()->environment(),
        'db_connection' => config('database.default'),
        'database' => config('database.connections.' . config('database.default') . '.database'),
    ];
});

Route::get('/debug/db', function () {
    return [
        'customers'   => Customer::count(),
        'messages'    => Message::count(),
        'webhook_logs'=> WebhookLog::count(),
    ];
});

Route::get('/debug/webhook-table', function () {
    return [
        'exists'  => Schema::hasTable('webhook_logs'),
        'columns' => Schema::getColumnListing('webhook_logs'),
    ];
});

Route::get('/debug/last-webhook', function () {
    return WebhookLog::latest()->first();
});