<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function send(Request $request, Customer $customer)
    {
        dd([
            'controller_reached' => true,
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $request->input('message'),
        ]);
    }
}