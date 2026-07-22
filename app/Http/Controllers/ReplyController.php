<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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

        return response()->json($result);
    }
}