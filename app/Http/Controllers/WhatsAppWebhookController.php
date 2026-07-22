<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify Meta webhook.
     */
    public function verify(Request $request)
    {
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
     * Receive WhatsApp webhook events.
     */
    public function receive(Request $request)
    {
        try {

            $payload = $request->all();


            Log::info('WhatsApp webhook received', [
                'payload' => $payload,
            ]);



            /*
            |--------------------------------------------------------------------------
            | Store webhook log
            |--------------------------------------------------------------------------
            */

            WebhookLog::create([
                'payload' => json_encode(
                    $payload,
                    JSON_PRETTY_PRINT
                ),
            ]);



            $value = data_get(
                $payload,
                'entry.0.changes.0.value'
            );



            if (!$value) {

                return response()->json([
                    'success' => true,
                ]);
            }



            /*
            |--------------------------------------------------------------------------
            | Handle WhatsApp Message Status Updates
            |--------------------------------------------------------------------------
            |
            | Updates:
            | sent
            | delivered
            | read
            | failed
            |
            */

            if (!empty($value['statuses'])) {


                foreach ($value['statuses'] as $status) {


                    Message::where(
                        'wa_message_id',
                        $status['id']
                    )
                    ->update([
                        'status' => $status['status'],
                    ]);


                    Log::info(
                        'WhatsApp message status updated',
                        [
                            'message_id' => $status['id'],
                            'status' => $status['status'],
                        ]
                    );
                }
            }




            /*
            |--------------------------------------------------------------------------
            | Ignore if no incoming messages
            |--------------------------------------------------------------------------
            */

            if (empty($value['messages'])) {

                return response()->json([
                    'success' => true,
                ]);

            }



            /*
            |--------------------------------------------------------------------------
            | Handle Incoming Messages
            |--------------------------------------------------------------------------
            */

            $contact = $value['contacts'][0] ?? [];



            foreach ($value['messages'] as $waMessage) {


                $customer = Customer::firstOrCreate(
                    [
                        'phone' => $waMessage['from'],
                    ],
                    [
                        'first_name' => data_get(
                            $contact,
                            'profile.name',
                            'WhatsApp User'
                        ),

                        'last_name' => '',

                        'email' => null,

                        'shopify_customer_id' => null,
                    ]
                );



                Message::create([

                    'customer_id' => $customer->id,

                    'wa_message_id' => $waMessage['id'],

                    'direction' => 'incoming',

                    'message' => data_get(
                        $waMessage,
                        'text.body',
                        ''
                    ),

                    'is_read' => false,

                    'status' => 'received',

                ]);


                Log::info(
                    'Incoming WhatsApp message stored',
                    [
                        'customer_id' => $customer->id,
                        'message_id' => $waMessage['id'],
                    ]
                );
            }



            return response()->json([
                'success' => true,
            ]);



        } catch (\Throwable $e) {


            Log::error(
                'WhatsApp webhook failed',
                [
                    'message' => $e->getMessage(),

                    'file' => $e->getFile(),

                    'line' => $e->getLine(),
                ]
            );


            return response()->json([
                'success' => false,

                'error' => $e->getMessage(),

            ], 500);

        }
    }
}