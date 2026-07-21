<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendText(string $to, string $message): array
    {
        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            config('whatsapp.api_version'),
            config('whatsapp.phone_number_id')
        );

        $response = Http::withToken(config('whatsapp.access_token'))
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ]);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }
}