<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify Meta webhook.
     */
   public function verify(Request $request)
{
    Log::info('Webhook verification request', [
        'query' => $request->query(),
        'all' => $request->all(),
        'headers' => $request->headers->all(),
    ]);

    $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
    $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
    $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

    Log::info('Parsed verification values', [
        'mode' => $mode,
        'token' => $token,
        'expected' => config('whatsapp.verify_token'),
        'challenge' => $challenge,
    ]);

    if (
        $mode === 'subscribe' &&
        $token === config('whatsapp.verify_token')
    ) {
        return response($challenge, 200)
            ->header('Content-Type', 'text/plain');
    }

    return response('Forbidden', 403);
}