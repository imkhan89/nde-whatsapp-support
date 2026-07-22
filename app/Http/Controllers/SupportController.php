<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class SupportController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('updated_at', 'desc')->get();

        return view('support.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customers = Customer::orderBy('updated_at', 'desc')->get();

        $messages = $customer->messages()
            ->orderBy('created_at')
            ->get();

        return view('support.index', compact(
            'customers',
            'customer',
            'messages'
        ));
    }
}