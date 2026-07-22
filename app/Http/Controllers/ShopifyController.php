<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Models\Order;
use App\Services\ShopifyWebhookService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopifyController extends Controller
{
    public function __construct(
        protected ShopifyWebhookService $shopifyWebhook,
        protected WhatsAppService $whatsapp
    ) {}

    public function orderCreated(Request $request): JsonResponse
    {
        $payload = $request->getContent();

        $hmac = $request->header(
            'X-Shopify-Hmac-Sha256'
        );

        if (!$this->shopifyWebhook->verify($payload, $hmac)) {

            Log::warning('Invalid Shopify webhook signature');

            return response()->json([
                'message' => 'Invalid webhook signature.',
            ], 401);
        }


        $shopifyOrder = json_decode(
            $payload,
            true
        );


        if (!is_array($shopifyOrder)) {

            return response()->json([
                'message' => 'Invalid payload.',
            ], 400);
        }


        DB::beginTransaction();


        try {

            $shopifyCustomer =
                $shopifyOrder['customer'] ?? [];


            $phone =
                $shopifyCustomer['phone']
                ?? data_get($shopifyOrder, 'shipping_address.phone')
                ?? data_get($shopifyOrder, 'billing_address.phone');


            $email =
                $shopifyCustomer['email'] ?? null;


            $shopifyCustomerId =
                $shopifyCustomer['id'] ?? null;


            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            $customer = null;


            if ($shopifyCustomerId) {

                $customer = Customer::where(
                    'shopify_customer_id',
                    $shopifyCustomerId
                )->first();

            }


            if (!$customer && $phone) {

                $customer = Customer::where(
                    'phone',
                    $phone
                )->first();

            }


            if (!$customer && $email) {

                $customer = Customer::where(
                    'email',
                    $email
                )->first();

            }


            if (!$customer) {

                $customer = new Customer();

            }


            $customer->shopify_customer_id =
                $shopifyCustomerId;

            $customer->first_name =
                $shopifyCustomer['first_name'] ?? '';

            $customer->last_name =
                $shopifyCustomer['last_name'] ?? '';

            $customer->email =
                $email ?? '';

            $customer->phone =
                $phone ?? '';


            $customer->save();



            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */


            $order = Order::updateOrCreate(

                [
                    'shopify_order_id' =>
                        $shopifyOrder['id'],
                ],

                [
                    'customer_id' =>
                        $customer->id,

                    'order_number' =>
                        $shopifyOrder['order_number'] ?? '',

                    'status' =>
                        $shopifyOrder['financial_status'] ?? '',

                    'total' =>
                        $shopifyOrder['total_price'] ?? 0,
                ]

            );


            DB::commit();



            /*
            |--------------------------------------------------------------------------
            | WhatsApp Confirmation
            |--------------------------------------------------------------------------
            */


            if (!empty($customer->phone)) {


                $messageText =
                    "Hello {$customer->first_name},\n\n"
                    ."Your order "
                    .($shopifyOrder['name'] ?? '')
                    ." has been received successfully.\n\n"
                    ."Order Amount: Rs "
                    .($shopifyOrder['total_price'] ?? '0')
                    ."\n\n"
                    ."Thank you for shopping with NDE Store.";


                Log::info(
                    'Preparing WhatsApp message',
                    [
                        'customer_id' =>
                            $customer->id,

                        'phone' =>
                            $customer->phone,

                        'order_id' =>
                            $order->id,
                    ]
                );


                $response =
                    $this->whatsapp->sendMessage(
                        $customer->phone,
                        $messageText
                    );


                Log::info(
                    'WhatsApp response received',
                    [
                        'response' =>
                            $response,
                    ]
                );


                if ($response) {


                    Message::create([

                        'customer_id' =>
                            $customer->id,

                        'wa_message_id' =>
                            data_get(
                                $response,
                                'messages.0.id'
                            ),

                        'direction' =>
                            'outgoing',

                        'message' =>
                            $messageText,

                        'is_read' =>
                            true,

                    ]);


                } else {


                    Log::warning(
                        'WhatsApp message failed',
                        [
                            'customer_id' =>
                                $customer->id,

                            'phone' =>
                                $customer->phone,
                        ]
                    );

                }

            } else {


                Log::warning(
                    'Customer phone missing',
                    [
                        'customer_id' =>
                            $customer->id,
                    ]
                );

            }



            Log::info(
                'Shopify order processed',
                [
                    'shopify_order_id' =>
                        $order->shopify_order_id,

                    'customer_id' =>
                        $customer->id,
                ]
            );


            return response()->json([
                'success' => true,
            ]);


        } catch (\Throwable $e) {


            DB::rollBack();


            Log::error(
                'Shopify order processing failed',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                ]
            );


            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage(),

            ],500);

        }
    }
}