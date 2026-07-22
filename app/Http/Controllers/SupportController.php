<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class SupportController extends Controller
{
    /**
     * Customer list
     */
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


        return view('support.index', [
            'customers' => $customers,
        ]);
    }


    /**
     * Open customer conversation
     */
    public function show(Customer $customer)
    {

        // Mark incoming messages as read
        $customer->messages()
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);


        // Refresh customer data
        $customer->refresh();


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



    /**
     * AJAX message refresh endpoint
     */
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