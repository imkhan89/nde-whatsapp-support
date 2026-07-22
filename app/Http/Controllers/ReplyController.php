<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function send(Request $request, Customer $customer, WhatsAppService $whatsapp)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:4096'
            ],
        ]);

        $result = $whatsapp->sendText(
            $customer->phone,
            $request->message
        );

        if (!$result['success']) {

            return back()->with(
                'error',
                'WhatsApp message failed'
            );

        }


        Message::create([
            'customer_id' => $customer->id,
            'wa_message_id' => $result['message_id'],
            'direction' => 'outgoing',
            'message' => $request->message,
        ]);


        return back();
    }
}