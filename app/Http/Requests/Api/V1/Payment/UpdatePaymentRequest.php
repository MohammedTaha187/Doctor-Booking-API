<?php

namespace App\Http\Requests\Api\V1\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'sometimes|exists:appointments,id',
            'gateway' => 'sometimes|in:paymob,stripe,paypal',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'gateway_order_id' => 'nullable|string|max:255',
            'amount' => 'sometimes|numeric',
            'currency' => 'nullable|string|size:3',
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'metadata' => 'nullable|array',
        ];
    }
}
