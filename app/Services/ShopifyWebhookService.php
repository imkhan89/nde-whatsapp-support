<?php

namespace App\Services;

class ShopifyWebhookService
{
    public function verify(string $payload, ?string $hmac): bool
    {
        $secret = env('SHOPIFY_WEBHOOK_SECRET');

        if (!$hmac || !$secret) {
            return false;
        }

        $calculated = base64_encode(
            hash_hmac('sha256', $payload, $secret, true)
        );

        return hash_equals($hmac, $calculated);
    }
}