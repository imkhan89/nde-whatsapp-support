<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/shopify/order-created', [ShopifyController::class, 'orderCreated']);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});

use App\Http\Controllers\WhatsAppController;

Route::post('/whatsapp/test', [WhatsAppController::class, 'test']);