<?php

namespace WepayCheckout\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use WepayCheckout\WepayClictopayClient;

/**
 * Example: create an invoice from your cart/checkout and redirect the browser to paymentLink.
 */
class WepayInvoiceController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'redirect_url' => ['required', 'url'],
            'customer.name' => ['required', 'string'],
            'customer.email' => ['required', 'email'],
            'customer.phoneNum' => ['nullable', 'string'],
            'customer.address' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.productId' => ['required'],
            'products.*.productName' => ['required', 'string'],
            'products.*.pricePerUnit' => ['required', 'string'],
            'products.*.productDiscount' => ['nullable', 'string'],
            'products.*.quanttiy' => ['required', 'string'],
            'products.*.totalPrice' => ['required', 'string'],
            'shipingPrice' => ['nullable', 'string'],
            'subTotal' => ['required', 'string'],
            'discount' => ['nullable', 'string'],
            'totalWithoutTax' => ['required', 'string'],
            'taxValue' => ['nullable', 'string'],
            'invoiceTotal' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $client = WepayClictopayClient::fromConfig();

        $invoice = [
            'merchantId' => config('wepay.merchant_id'),
            'userId' => null,
            'customer' => [
                'name' => $validated['customer']['name'],
                'email' => $validated['customer']['email'],
                'phoneNum' => $validated['customer']['phoneNum'] ?? '',
                'address' => $validated['customer']['address'] ?? '',
                'language' => app()->getLocale(),
                'vatId' => '',
            ],
            'products' => array_values($validated['products']),
            'shipingPrice' => $validated['shipingPrice'] ?? '0.00',
            'sendBy' => 'email',
            'totalItem' => count($validated['products']),
            'subTotal' => $validated['subTotal'],
            'discount' => $validated['discount'] ?? '0.00',
            'totalWithoutTax' => $validated['totalWithoutTax'],
            'taxValue' => $validated['taxValue'] ?? '0.00',
            'invoiceTotal' => $validated['invoiceTotal'],
            'invoiceStatus' => false,
            'paymentLink' => '',
            'description' => $validated['description'] ?? 'Invoice',
            'status' => 'created',
            'signature' => '',
            'redirectUrl' => $validated['redirect_url'],
        ];

        try {
            $response = $client->createInvoice($invoice);
            $link = WepayClictopayClient::extractPaymentLink($response);
            if (!$link) {
                abort(502, 'WEPAY: missing paymentLink in response');
            }
        } catch (RequestException $e) {
            abort(502, 'WEPAY API error: ' . $e->response->body());
        }

        return redirect()->away($link);
    }

    public function status(string $invoiceId)
    {
        $client = WepayClictopayClient::fromConfig();

        try {
            return response()->json($client->invoiceStatus($invoiceId));
        } catch (RequestException $e) {
            return response()->json(
                ['error' => $e->response->json() ?? $e->response->body()],
                $e->response->status()
            );
        }
    }
}
