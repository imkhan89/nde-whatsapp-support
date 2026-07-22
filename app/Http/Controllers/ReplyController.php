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


        $response = $whatsapp->sendMessage(
            $customer->phone,
            $request->message
        );


        if (!$response) {

            return redirect()
                ->route('support.show', $customer)
                ->withInput()
                ->with(
                    'error',
                    'WhatsApp message failed.'
                );
        }



        Message::create([
            'customer_id' => $customer->id,

            'wa_message_id' =>
                data_get(
                    $response,
                    'messages.0.id'
                ),

            'direction' => 'outgoing',

            'message' => $request->message,

            'is_read' => true,

            'status' => 'sent',
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



        $response = $whatsapp->sendMessage(
            $customer->phone,
            $request->message
        );



        if (!$response) {

            return response()->json([
                'success' => false,

                'error' => 'WhatsApp message failed.',

            ], 400);

        }




        $message = Message::create([

            'customer_id' => $customer->id,


            'wa_message_id' =>
                data_get(
                    $response,
                    'messages.0.id'
                ),


            'direction' => 'outgoing',


            'message' => $request->message,


            'is_read' => true,


            'status' => 'sent',

        ]);




        return response()->json([

            'success' => true,


            'message' => [

                'id' => $message->id,


                'text' => $message->message,


                'direction' => $message->direction,


                'status' => $message->status,


                'created_at' =>
                    $message->created_at
                        ->format('d M Y H:i'),

            ],

        ]);
    }
}