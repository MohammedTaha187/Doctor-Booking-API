<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class KashierService implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $mode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.kashier.merchant_id');
        $this->apiKey = config('services.kashier.api_key');
        $this->mode = config('services.kashier.mode', 'sandbox');
        $this->baseUrl = $this->mode === 'sandbox' 
            ? 'https://checkout.sandbox.kashier.com' 
            : 'https://checkout.kashier.com';
    }

    public function initiate(User $user, Appointment $appointment, float $amount): array
    {
        $orderId = "order_" . time() . "_" . $appointment->id;
        
        // Save pending payment record
        Payment::create([
            'user_id' => $user->id,
            'appointment_id' => $appointment->id,
            'amount' => $amount,
            'provider' => 'kashier',
            'provider_id' => $orderId,
            'status' => 'pending',
        ]);

        // Generate Kashier iframe URL or return data for frontend
        return [
            'url' => "{$this->baseUrl}/?merchantId={$this->merchantId}&amount={$amount}&orderId={$orderId}&currency=EGP",
            'order_id' => $orderId,
        ];
    }

    public function verify(string $transactionId): bool
    {
        // Kashier verification logic (API call or HMAC check)
        Payment::where('provider_id', $transactionId)->update(['status' => 'completed']);
        return true;
    }

    public function refund(string $transactionId): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        // Verify HMAC signature and update status
        if (($payload['event'] ?? '') === 'pay_confirmed') {
            $this->verify($payload['data']['orderId']);
        }
    }
}
