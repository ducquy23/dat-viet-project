<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PayOS\PayOS;
use PayOS\Exceptions\WebhookException;
use Throwable;

class PayOSService
{
    protected PayOS $client;
    protected string $clientId;
    protected string $apiKey;
    protected string $checksumKey;
    protected string $baseUrl;
    protected int $expireMinutes;

    public function __construct()
    {
        $this->clientId = (string) config('payos.client_id');
        $this->apiKey = (string) config('payos.api_key');
        $this->checksumKey = (string) config('payos.checksum_key');
        $this->baseUrl = rtrim((string) config('payos.base_url', ''), '/');
        $this->expireMinutes = (int) config('payos.expire_minutes', 10);

        $this->client = new PayOS(
            clientId: $this->clientId,
            apiKey: $this->apiKey,
            checksumKey: $this->checksumKey,
            partnerCode: null,
            baseURL: $this->baseUrl ?: null,
            logger: Log::channel()
        );
    }

    public function createPayment(int $amount, string $description, string $returnUrl = null, string $cancelUrl = null): array
    {
        $orderCode = $this->generateOrderCode();
        $expiredAt = Carbon::now()->addMinutes($this->expireMinutes);

        $returnUrl = $returnUrl ?: config('payos.return_url', config('app.url') . '/payment/success');
        $cancelUrl = $cancelUrl ?: config('payos.cancel_url', config('app.url') . '/payment/cancel');

        // PayOS giới hạn mô tả 25 ký tự
        $shortDesc = mb_substr($description, 0, 25);

        $payload = [
            'amount' => $amount,
            'orderCode' => (int) $orderCode,
            'description' => $shortDesc,
            'returnUrl' => $returnUrl,
            'cancelUrl' => $cancelUrl,
            'expiredAt' => $expiredAt->timestamp,
        ];

        $response = $this->client->paymentRequests->create($payload, ['asArray' => true]);

        Log::info('payos.create_payment.response', [
            'body' => $response,
        ]);

        return [
            'order_code' => $response['orderCode'] ?? $orderCode,
            'qr_url' => $response['qrCode'] ?? null,
            'checkout_url' => $response['checkoutUrl'] ?? null,
            'expired_at' => $expiredAt,
            'raw' => $response,
        ];
    }

    public function verifyWebhook(array $payload): ?array
    {
        try {
            $data = $this->client->webhooks->verify($payload, ['asArray' => true]);
            return is_array($data) ? $data : null;
        } catch (WebhookException|Throwable $e) {
            Log::warning('payos.webhook.verify_failed', ['message' => $e->getMessage(), 'payload' => $payload]);
            return null;
        }
    }

    protected function generateOrderCode(): string
    {
        return (string) now()->timestamp . random_int(1000, 9999);
    }
}

