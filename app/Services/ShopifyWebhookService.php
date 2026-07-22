<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ShopifyWebhookService
{
    public function verify(string $payload, ?string $hmac): bool
    {
        if (!$hmac) {
            return false;
        }

        $secret = config('services.shopify.webhook_secret');

        if (!$secret) {
            Log::error('Shopify webhook secret missing');

            return false;
        }

        $calculated = base64_encode(
            hash_hmac(
                'sha256',
                $payload,
                $secret,
                true
            )
        );

        return hash_equals(
            $calculated,
            $hmac
        );
    }
}