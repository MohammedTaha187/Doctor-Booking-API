<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobService implements PaymentGatewayInterface
{
    private string $apiKey;

    private string $integrationId;

    private string $iframeId;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key', '');
        $this->integrationId = config('services.paymob.integration_id', '');
        $this->iframeId = config('services.paymob.iframe_id', '');
    }

    private function http(): PendingRequest
    {
        return Http::timeout(15)
            ->connectTimeout(5)
            ->retry(3, 250);
    }

    private function assertConfigured(): void
    {
        if ($this->apiKey === '' || $this->integrationId === '' || $this->iframeId === '') {
            throw new RuntimeException('Paymob configuration is incomplete.');
        }
    }

    public function initiate(User $user, Appointment $appointment, float $amount): array
    {
        $this->assertConfigured();

        $firstName = explode(' ', trim($user->name))[0] ?: $user->name;
        $lastName = explode(' ', trim($user->name))[1] ?? $firstName;

        $authResponse = $this->http()->post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ])->throw();
        $token = $authResponse->json('token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Failed to obtain Paymob auth token.');
        }

        $orderResponse = $this->http()->withToken($token)->post('https://accept.paymob.com/api/ecommerce/orders', [
            'amount_cents' => (int) ($amount * 100),
            'currency' => 'EGP',
            'merchant_order_id' => $appointment->id,
            'items' => [],
        ])->throw();
        $orderId = $orderResponse->json('id');

        if (! is_string($orderId) && ! is_int($orderId)) {
            throw new RuntimeException('Failed to create Paymob order.');
        }

        $paymentKeyResponse = $this->http()->withToken($token)->post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'amount_cents' => (int) ($amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'email' => $user->email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $user->phone ?? 'N/A',
                'apartment' => 'NA', 'floor' => 'NA', 'street' => 'NA',
                'building' => 'NA', 'shipping_method' => 'NA', 'postal_code' => 'NA',
                'city' => 'NA', 'country' => 'NA', 'state' => 'NA',
            ],
            'currency' => 'EGP',
            'integration_id' => $this->integrationId,
        ])->throw();
        $paymentKey = $paymentKeyResponse->json('token');

        if (! is_string($paymentKey) || $paymentKey === '') {
            throw new RuntimeException('Failed to obtain Paymob payment key.');
        }

        Payment::updateOrCreate([
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
        ], [
            'user_id' => $user->id,
            'gateway_order_id' => (string) $orderId,
            'amount' => $amount,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);

        return [
            'url' => "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}",
            'order_id' => (string) $orderId,
        ];
    }

    public function verify(string $transactionId): bool
    {
        return Payment::where('gateway_transaction_id', $transactionId)
            ->where('status', 'completed')
            ->exists();
    }

    public function refund(string $transactionId): bool
    {
        // @todo Implement Paymob refund API integration
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            $isSuccess = (bool) ($payload['obj']['success'] ?? false);
            $transactionId = $payload['obj']['id'] ?? null;
            $orderId = $payload['obj']['order']['id'] ?? null;

            if (! is_string($orderId) || $orderId === '') {
                return;
            }

            if ($isSuccess) {
                Payment::where('gateway_order_id', $orderId)->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => is_string($transactionId) ? $transactionId : null,
                ]);
            } else {
                Payment::where('gateway_order_id', $orderId)->update([
                    'status' => 'failed',
                ]);
            }
        });
    }
}
