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
        $customer->load('messages');

        $customers = Customer::orderBy('updated_at', 'desc')->get();

        return view('support.index', compact(
            'customers',
            'customer'
        ));
    }
}