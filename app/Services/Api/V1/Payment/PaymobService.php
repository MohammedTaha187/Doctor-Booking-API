<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

    public function initiate(User $user, Appointment $appointment, float $amount): array
    {
        // Step 1: Get Paymob auth token
        $authResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);
        $token = $authResponse->json('token');

        // Step 2: Create order
        $orderResponse = Http::withToken($token)->post('https://accept.paymob.com/api/ecommerce/orders', [
            'amount_cents' => (int) ($amount * 100),
            'currency' => 'EGP',
            'merchant_order_id' => $appointment->id,
            'items' => [],
        ]);
        $orderId = $orderResponse->json('id');

        // Step 3: Get payment key
        $paymentKeyResponse = Http::withToken($token)->post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'amount_cents' => (int) ($amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'email' => $user->email,
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'N/A',
                'phone_number' => $user->phone ?? 'N/A',
                'apartment' => 'NA', 'floor' => 'NA', 'street' => 'NA',
                'building' => 'NA', 'shipping_method' => 'NA', 'postal_code' => 'NA',
                'city' => 'NA', 'country' => 'NA', 'state' => 'NA',
            ],
            'currency' => 'EGP',
            'integration_id' => $this->integrationId,
        ]);
        $paymentKey = $paymentKeyResponse->json('token');

        // Record the pending payment
        Payment::create([
            'user_id' => $user->id,
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
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
        // In production: call Paymob refund API
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            $isSuccess = $payload['obj']['success'] ?? false;
            $transactionId = (string) ($payload['obj']['id'] ?? null);
            $orderId = (string) ($payload['obj']['order']['id'] ?? null);

            if ($isSuccess) {
                Payment::where('gateway_order_id', $orderId)->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $transactionId,
                ]);
            } else {
                Payment::where('gateway_order_id', $orderId)->update([
                    'status' => 'failed',
                ]);
            }
        });
    }
}
