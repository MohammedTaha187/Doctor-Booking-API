<?php

namespace App\Services\Api\V1\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;

class PaymentService
{
    /**
     * Resolve the correct payment gateway by name.
     * Usage: $this->paymentService->gateway('stripe')->initiate(...)
     */
    public function gateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe' => app(StripeService::class),
            'paymob' => app(PaymobService::class),
            default => throw new \InvalidArgumentException("Unsupported payment gateway: {$name}"),
        };
    }
}
