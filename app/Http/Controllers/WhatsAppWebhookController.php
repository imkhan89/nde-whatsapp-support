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
        $mode = $request->query('hub.mode');
        $token = $request->query('hub.verify_token');
        $challenge = $request->query('hub.challenge');

        if (
            $mode === 'subscribe' &&
            $token === config('whatsapp.verify_token')
        ) {
            Log::info('WhatsApp webhook verified.');

            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed.', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Receive incoming webhook events.
     */
    public function receive(Request $request)
    {
        Log::info('Incoming WhatsApp webhook', [
            'payload' => $request->all(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}