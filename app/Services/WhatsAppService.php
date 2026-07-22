<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendMessage(
        string $phone,
        string $message
    ): ?array {

        try {

            if (empty($phone)) {
                Log::warning(
                    'WhatsApp message skipped: empty phone number'
                );

                return null;
            }


            $version = config('whatsapp.api_version');

            $phoneNumberId = config(
                'whatsapp.phone_number_id'
            );


            $response = Http::withToken(
                config('whatsapp.access_token')
            )
            ->post(
                "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages",
                [
                    'messaging_product' => 'whatsapp',

                    'recipient_type' => 'individual',

                    'to' => $phone,

                    'type' => 'text',

                    'text' => [
                        'preview_url' => false,

                        'body' => $message,
                    ],
                ]
            );


            if (!$response->successful()) {

                Log::error(
                    'WhatsApp API request failed',
                    [
                        'status' =>
                            $response->status(),

                        'response' =>
                            $response->json(),
                    ]
                );

                return null;
            }


            return $response->json();


        } catch (\Throwable $e) {


            Log::error(
                'WhatsApp send exception',
                [
                    'error' =>
                        $e->getMessage(),
                ]
            );


            return null;
        }
    }
}