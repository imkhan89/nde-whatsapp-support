<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Services\ShopifyWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopifyController extends Controller
{
    public function __construct(
        protected ShopifyWebhookService $shopifyWebhook
    ) {}

    public function orderCreated(Request $request): JsonResponse
    {
        $payload = $request->getContent();

        $hmac = $request->header('X-Shopify-Hmac-Sha256');

        if (! $this->shopifyWebhook->verify($payload, $hmac)) {
            return response()->json([
                'message' => 'Invalid webhook signature.',
            ], 401);
        }

        $shopifyOrder = json_decode($payload, true);

        if (! is_array($shopifyOrder)) {
            return response()->json([
                'message' => 'Invalid payload.',
            ], 400);
        }

        $customer = Customer::updateOrCreate(
            [
                'shopify_customer_id' => $shopifyOrder['customer']['id'] ?? null,
            ],
            [
                'first_name' => $shopifyOrder['customer']['first_name'] ?? '',
                'last_name'  => $shopifyOrder['customer']['last_name'] ?? '',
                'email'      => $shopifyOrder['customer']['email'] ?? '',
                'phone'      => $shopifyOrder['customer']['phone']
                    ?? $shopifyOrder['shipping_address']['phone']
                    ?? $shopifyOrder['billing_address']['phone']
                    ?? '',
            ]
        );

        Order::updateOrCreate(
            [
                'shopify_order_id' => $shopifyOrder['id'],
            ],
            [
                'customer_id'  => $customer->id,
                'order_number' => $shopifyOrder['order_number'] ?? '',
                'status'       => $shopifyOrder['financial_status'] ?? '',
                'total'        => $shopifyOrder['total_price'] ?? 0,
            ]
        );

        Log::info('Order saved successfully.', [
            'order_id' => $shopifyOrder['id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}