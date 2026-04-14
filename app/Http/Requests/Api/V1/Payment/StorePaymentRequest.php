<?php

namespace App\Http\Requests\Api\V1\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'gateway' => 'required|in:paymob,stripe,paypal',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'gateway_order_id' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'currency' => 'nullable|string|size:3',
            'status' => 'required|in:pending,completed,failed,refunded',
            'metadata' => 'nullable|array',
        ];
    }
}
