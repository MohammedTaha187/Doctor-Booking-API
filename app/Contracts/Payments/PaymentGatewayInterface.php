<?php

namespace App\Contracts\Payments;

use App\Models\Appointment;
use App\Models\User;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment and return gateway URL and order info.
     *
     * @return array{url: string, order_id: string}
     */
    public function initiate(User $user, Appointment $appointment, float $amount): array;

    /**
     * Verify a transaction by its gateway transaction ID.
     */
    public function verify(string $transactionId): bool;

    /**
     * Issue a refund for a completed transaction.
     */
    public function refund(string $transactionId): bool;

    /**
     * Handle an incoming webhook payload from the gateway.
     */
    public function handleWebhook(array $payload): void;
}
