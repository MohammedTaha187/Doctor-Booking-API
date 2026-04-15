<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PayPalService implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $secret;
    protected string $mode;

    public function __construct()
    {
        $this->mode = config('services.paypal.mode', 'sandbox');
        $this->baseUrl = $this->mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
    }

    public function initiate(User $user, Appointment $appointment, float $amount): array
    {
        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => (string) $amount,
                    ],
                    'description' => "Appointment payment for {$appointment->id}",
                ],
            ],
            'application_context' => [
                'return_url' => url('/payment/success'),
                'cancel_url' => url('/payment/cancel'),
            ],
        ];

        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->post($this->baseUrl . '/v2/checkout/orders', $orderData);

        if (!$response->successful()) {
            throw new \Exception('Failed to create PayPal order');
        }

        $order = $response->json();

        // Save payment record
        Payment::create([
            'user_id' => $user->id,
            'appointment_id' => $appointment->id,
            'amount' => $amount,
            'provider' => 'paypal',
            'provider_id' => $order['id'],
            'status' => 'pending',
        ]);

        return [
            'url' => $order['links'][1]['href'], // Approval URL
            'order_id' => $order['id'],
        ];
    }

    public function verify(string $transactionId): bool
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->post($this->baseUrl . "/v2/checkout/orders/{$transactionId}/capture");

        if (!$response->successful()) {
            return false;
        }

        $capture = $response->json();
        $status = $capture['status'] === 'COMPLETED';

        if ($status) {
            Payment::where('provider_id', $transactionId)->update(['status' => 'completed']);
        }

        return $status;
    }

    public function refund(string $transactionId): bool
    {
        // Simple refund placeholder
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        // Handle webhook notification logic
        if (($payload['event_type'] ?? '') === 'CHECKOUT.ORDER.APPROVED') {
            $this->verify($payload['resource']['id']);
        }
    }
}
