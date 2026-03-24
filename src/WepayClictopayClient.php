<?php

namespace WepayCheckout;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * WePay / ClicToPay Invoice Checkout API (server-side signing and calls).
 */
class WepayClictopayClient
{
    public function __construct(
        private string $baseUrl,
        private string $merchantId,
        private string $secretKey,
        private string $apiKey
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) config('wepay.base_url'),
            (string) config('wepay.merchant_id'),
            (string) config('wepay.secret_key'),
            (string) config('wepay.api_key')
        );
    }

    /**
     * @return array{data: string, signature: string}
     */
    public function buildSignedBody(array $invoicePayload): array
    {
        $json = json_encode($invoicePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \RuntimeException('WEPAY: invalid JSON payload.');
        }
        $encoded = base64_encode($json);
        $raw = hash_hmac('sha256', $encoded, $this->secretKey, true);

        return [
            'data' => $encoded,
            'signature' => strtoupper(bin2hex($raw)),
        ];
    }

    /**
     * POST /merchants/invoice/online
     *
     * @return array<string, mixed>
     */
    public function createInvoice(array $invoicePayload): array
    {
        $url = $this->baseUrl . '/merchants/invoice/online';
        $body = $this->buildSignedBody($invoicePayload);

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->post($url, $body);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json() ?? [];
    }

    /**
     * GET /merchants/{merchantId}/invoice/{invoiceId}/status
     *
     * @return array<string, mixed>
     */
    public function invoiceStatus(string $invoiceId): array
    {
        $url = sprintf(
            '%s/merchants/%s/invoice/%s/status',
            $this->baseUrl,
            rawurlencode($this->merchantId),
            rawurlencode($invoiceId)
        );

        $response = Http::acceptJson()
            ->timeout(30)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->get($url);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json() ?? [];
    }

    public static function extractPaymentLink(array $response): ?string
    {
        foreach (['paymentLink', 'payment_link', 'url', 'link'] as $k) {
            if (!empty($response[$k]) && is_string($response[$k])) {
                return $response[$k];
            }
        }
        $data = $response['data'] ?? null;
        if (is_array($data)) {
            foreach (['paymentLink', 'payment_link', 'url', 'link'] as $k) {
                if (!empty($data[$k]) && is_string($data[$k])) {
                    return $data[$k];
                }
            }
        }

        return null;
    }

    public static function extractInvoiceId(array $response): ?string
    {
        foreach (['invoiceId', 'invoice_id', 'id'] as $k) {
            if (!empty($response[$k])) {
                return (string) $response[$k];
            }
        }
        $data = $response['data'] ?? null;
        if (is_array($data)) {
            foreach (['invoiceId', 'invoice_id', 'id'] as $k) {
                if (!empty($data[$k])) {
                    return (string) $data[$k];
                }
            }
        }

        return null;
    }
}
