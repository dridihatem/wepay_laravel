<?php

namespace WepayCheckout\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Register this URL with the WePay team. They POST JSON when status changes.
 */
class WepayWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = (string) config('wepay.webhook_secret');
        if ($secret !== '') {
            // Add verification when WePay documents signature headers or shared secret checks.
        }

        $payload = $request->json()->all();
        if ($payload === []) {
            $payload = json_decode($request->getContent(), true) ?? [];
        }

        $invoiceId = isset($payload['invoiceId']) ? (string) $payload['invoiceId'] : '';
        $status = isset($payload['status']) ? (string) $payload['status'] : '';

        if ($invoiceId === '') {
            return response()->json(['ok' => false, 'error' => 'missing_invoiceId'], 400);
        }

        /*
        | Update your Order / Payment model here, e.g.:
        | $order = Order::where('wepay_invoice_id', $invoiceId)->first();
        | if ($order && str_contains(strtolower($status), 'success')) { ... mark paid }
        */

        return response()->json(['ok' => true, 'received' => true]);
    }
}
