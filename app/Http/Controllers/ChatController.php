<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsapp
    ) {}

    /**
     * Display customer inbox
     */
    public function index()
    {
        $customers = Customer::whereHas('messages')
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('direction', 'incoming')
                          ->where('is_read', false);
                }
            ])
            ->latest()
            ->get();

        return view('chat.index', compact('customers'));
    }


    /**
     * Open conversation
     */
    public function show(Customer $customer)
    {
        $messages = Message::where(
                'customer_id',
                $customer->id
            )
            ->orderBy('created_at')
            ->get();

        // Mark incoming messages as read
        Message::where('customer_id', $customer->id)
            ->where('direction', 'incoming')
            ->update([
                'is_read' => true
            ]);

        return view('chat.show', [
            'customer' => $customer,
            'messages' => $messages,
        ]);
    }


    /**
     * Send WhatsApp reply
     */
    public function send(
        Request $request,
        Customer $customer
    ) {

        $request->validate([
            'message' => [
                'required',
                'string'
            ],
        ]);


        $response = $this->whatsapp->sendMessage(
            $customer->phone,
            $request->message
        );


        if ($response) {

            Message::create([
                'customer_id' => $customer->id,
                'wa_message_id' =>
                    data_get(
                        $response,
                        'messages.0.id'
                    ),

                'direction' => 'outgoing',

                'message' =>
                    $request->message,

                'is_read' => true,
            ]);

        }


        return back();
    }
}