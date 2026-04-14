<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StripeService implements PaymentGatewayInterface
{
    public function initiate(User $user, Appointment $appointment, float $amount): array
    {
        // In production, use the Stripe SDK:
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        // $session = \Stripe\Checkout\Session::create([...]);

        $orderId = 'stripe_'.$appointment->id.'_'.time();

        Payment::create([
            'user_id' => $user->id,
            'appointment_id' => $appointment->id,
            'gateway' => 'stripe',
            'gateway_order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        return [
            'url' => 'https://checkout.stripe.com/pay/'.$orderId,
            'order_id' => $orderId,
        ];
    }

    public function verify(string $transactionId): bool
    {
        // In production: retrieve and check the Stripe PaymentIntent status
        return true;
    }

    public function refund(string $transactionId): bool
    {
        // In production: use \Stripe\Refund::create(['payment_intent' => $transactionId])
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'] ?? null;

        DB::transaction(function () use ($payload, $event) {
            if ($event === 'payment_intent.succeeded') {
                $transactionId = $payload['data']['object']['id'] ?? null;
                Payment::where('gateway_transaction_id', $transactionId)
                    ->update(['status' => 'completed']);
            }
        });
    }
}
