/*
|--------------------------------------------------------------------------
| Send WhatsApp
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

    Log::info('Preparing to send WhatsApp message', [
        'customer_id' => $customer->id,
        'customer_name' => trim($customer->first_name . ' ' . $customer->last_name),
        'phone' => $customer->phone,
        'order_id' => $order->id,
        'shopify_order_id' => $order->shopify_order_id,
    ]);

    $response = $this->whatsapp->sendMessage(
        $customer->phone,
        $messageText
    );

    Log::info('WhatsApp service response', [
        'response' => $response,
    ]);

    if ($response) {

        Message::create([
            'customer_id' => $customer->id,
            'wa_message_id' => data_get($response, 'messages.0.id'),
            'direction' => 'outgoing',
            'message' => $messageText,
            'is_read' => true,
        ]);

        Log::info('Outgoing WhatsApp message stored', [
            'customer_id' => $customer->id,
            'wa_message_id' => data_get($response, 'messages.0.id'),
        ]);
    } else {

        Log::warning('WhatsApp message was not sent', [
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
        ]);
    }

} else {

    Log::warning('Customer has no phone number', [
        'customer_id' => $customer->id,
    ]);
}