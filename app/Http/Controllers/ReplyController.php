<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * Normal form reply
     */
    public function send(
        Request $request,
        Customer $customer,
        WhatsAppService $whatsapp
    ) {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:4096',
            ],
        ]);

        $result = $whatsapp->sendText(
            $customer->phone,
            $request->message
        );

        if (! $result['success']) {

            $errorMessage = data_get(
                $result,
                'body.error.message',
                'Unknown WhatsApp API error.'
            );

            return redirect()
                ->route('support.show', $customer)
                ->withInput()
                ->with(
                    'error',
                    "WhatsApp Error ({$result['status']}): {$errorMessage}"
                );
        }


        Message::create([
            'customer_id'   => $customer->id,
            'wa_message_id' => $result['message_id'],
            'direction'     => 'outgoing',
            'message'       => $request->message,
        ]);


        return redirect()
            ->route('support.show', $customer)
            ->with(
                'success',
                'Message sent successfully.'
            );
    }


    /**
     * AJAX reply endpoint
     */
    public function sendAjax(
        Request $request,
        Customer $customer,
        WhatsAppService $whatsapp
    ) {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:4096',
            ],
        ]);


        $result = $whatsapp->sendText(
            $customer->phone,
            $request->message
        );


        if (! $result['success']) {

            return response()->json([
                'success' => false,
                'status' => $result['status'],
                'error' => data_get(
                    $result,
                    'body.error.message',
                    'Unknown WhatsApp API error.'
                ),
            ], 400);

        }


        $message = Message::create([
            'customer_id'   => $customer->id,
            'wa_message_id' => $result['message_id'],
            'direction'     => 'outgoing',
            'message'       => $request->message,
        ]);


        return response()->json([
            'success' => true,

            'message' => [
                'id' => $message->id,
                'text' => $message->message,
                'direction' => $message->direction,
                'created_at' => $message->created_at->format('d M Y H:i'),
            ],
        ]);
    }
}