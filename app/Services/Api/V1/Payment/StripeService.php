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

        $orderId = 'stripe_'.$appointment->id.'_'.time();

        Payment::updateOrCreate([
            'appointment_id' => $appointment->id,
            'gateway' => 'stripe',
        ], [
            'user_id' => $user->id,
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
        // @todo Implement Stripe PaymentIntent verification logic if needed
        return true;
    }

    public function refund(string $transactionId): bool
    {
        // @todo Implement Stripe Refund logic via SDK
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'] ?? null;

        DB::transaction(function () use ($payload, $event) {
            if ($event !== 'payment_intent.succeeded') {
                return;
            }

            $transactionId = $payload['data']['object']['id'] ?? null;

            if (! is_string($transactionId) || $transactionId === '') {
                return;
            }

            Payment::where('gateway_transaction_id', $transactionId)
                ->whereIn('status', ['pending', 'failed'])
                ->update(['status' => 'completed']);
        });
    }
}
