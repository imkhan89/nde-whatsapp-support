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
        // PHP converts dots in query parameters to underscores.
        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        if (
            $mode === 'subscribe' &&
            $token === config('whatsapp.verify_token')
        ) {
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Receive incoming WhatsApp webhook events.
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