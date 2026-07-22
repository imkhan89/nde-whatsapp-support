<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class SupportController extends Controller
{
    public function index()
    {
        $customers = Customer::with([
                'messages' => function ($query) {
                    $query->latest();
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('direction', 'incoming')
                          ->where('is_read', false);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();


        return view('support.index', compact('customers'));
    }


    public function show(Customer $customer)
    {
        // Mark incoming messages as read when opened
        $customer->messages()
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);


        $customers = Customer::with([
                'messages' => function ($query) {
                    $query->latest();
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('direction', 'incoming')
                          ->where('is_read', false);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();


        $messages = $customer->messages()
            ->orderBy('created_at', 'asc')
            ->get();


        return view('support.index', [
            'customers' => $customers,
            'customer'  => $customer,
            'messages'  => $messages,
        ]);
    }


    public function messages(Customer $customer)
    {
        $messages = $customer->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {

                return [
                    'id' => $message->id,
                    'direction' => $message->direction,
                    'message' => $message->message,
                    'created_at' => $message->created_at
                        ->format('d M Y H:i'),
                ];

            });


        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}