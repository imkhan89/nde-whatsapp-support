<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $data = $response->json();

        Log::info('WhatsApp send response', [
            'to' => $to,
            'response' => $data,
        ]);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'message_id' => data_get($data, 'messages.0.id'),
            'body' => $data,
        ];
    }
}