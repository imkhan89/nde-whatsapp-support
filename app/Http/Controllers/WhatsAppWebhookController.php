<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
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

        $value = data_get($request->all(), 'entry.0.changes.0.value');

        // Ignore status updates or empty webhook events
        if (!$value || empty($value['messages'])) {
            return response()->json([
                'success' => true,
            ]);
        }

        $waMessage = $value['messages'][0];
        $contact = $value['contacts'][0] ?? [];

        $customer = Customer::firstOrCreate(
            [
                'phone' => $waMessage['from'],
            ],
            [
                'first_name' => data_get($contact, 'profile.name', 'WhatsApp User'),
                'last_name' => '',
                'email' => null,
                'shopify_customer_id' => null,
            ]
        );

        Message::create([
            'customer_id'   => $customer->id,
            'wa_message_id' => $waMessage['id'],
            'direction'     => 'incoming',
            'message'       => data_get($waMessage, 'text.body', ''),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}