<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsApp
    ) {}

    public function test(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $result = $this->whatsApp->sendText(
            $request->input('phone'),
            'Hello! This is a test message from the NDE WhatsApp Support System.'
        );

        return response()->json(
            $result,
            $result['success'] ? 200 : $result['status']
        );
    }
}