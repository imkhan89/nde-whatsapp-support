<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendMessage(string $phone, string $message): ?array
    {
        try {

            /*
             * Normalize WhatsApp phone number
             * Removes spaces, +, -, brackets etc.
             */
            $phone = preg_replace('/[^0-9]/', '', $phone);


            /*
             * Pakistan number correction
             *
             * 0308xxxxxxx  → 92308xxxxxxx
             * 92308xxxxxxx → unchanged
             */
            if (str_starts_with($phone, '03')) {

                $phone = '92' . substr($phone, 1);

            }


            Log::info('WhatsApp send initiated', [
                'phone' => $phone,
            ]);


            if (empty($phone)) {

                Log::warning(
                    'WhatsApp message skipped: empty phone number'
                );

                return null;
            }



            $version = config('whatsapp.api_version');

            $phoneNumberId = config('whatsapp.phone_number_id');

            $accessToken = config('whatsapp.access_token');



            Log::info('WhatsApp configuration loaded', [
                'api_version' => $version,
                'phone_number_id' => $phoneNumberId,
                'token_prefix' => substr($accessToken, 0, 20) . '...',
            ]);



            $url = "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";



            $payload = [

                'messaging_product' => 'whatsapp',

                'recipient_type' => 'individual',

                'to' => $phone,

                'type' => 'text',

                'text' => [

                    'preview_url' => false,

                    'body' => $message,

                ],

            ];



            Log::info('Sending WhatsApp API request', [

                'url' => $url,

                'payload' => $payload,

            ]);



            $response = Http::withToken($accessToken)

                ->acceptJson()

                ->post($url, $payload);



            Log::info('WhatsApp API responded', [

                'status' => $response->status(),

                'successful' => $response->successful(),

            ]);



            if (!$response->successful()) {


                Log::error('WhatsApp API request failed', [

                    'status' => $response->status(),

                    'body' => $response->body(),

                    'json' => $response->json(),

                    'headers' => $response->headers(),

                ]);


                return null;

            }



            Log::info('WhatsApp message sent successfully', [

                'response' => $response->json(),

            ]);



            return $response->json();



        } catch (\Throwable $e) {


            Log::error('WhatsApp send exception', [

                'message' => $e->getMessage(),

                'file' => $e->getFile(),

                'line' => $e->getLine(),

                'trace' => $e->getTraceAsString(),

            ]);



            return null;

        }
    }
}