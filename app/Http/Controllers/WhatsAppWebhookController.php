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
    return response()->json([
        'all' => $request->all(),
        'query' => $request->query(),
        'hub.mode' => $request->query('hub.mode'),
        'hub_mode' => $request->query('hub_mode'),
        'hub.verify_token' => $request->query('hub.verify_token'),
        'hub_verify_token' => $request->query('hub_verify_token'),
        'config_verify_token' => config('whatsapp.verify_token'),
    ]);
}
}