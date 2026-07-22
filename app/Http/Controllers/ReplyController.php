<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
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

        // TEMPORARY DEBUG - REMOVE AFTER TESTING
        dd($result);

        if (! $result['success']) {

            $errorMessage = data_get(
                $result,
                'body.error.message',
                'Unknown WhatsApp API error.'
            );

            return back()
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

        return back()->with(
            'success',
            'Message sent successfully.'
        );
    }
}