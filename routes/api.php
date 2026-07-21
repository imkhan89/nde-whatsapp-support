<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\WhatsAppWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/shopify/order-created', [ShopifyController::class, 'orderCreated']);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok'
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