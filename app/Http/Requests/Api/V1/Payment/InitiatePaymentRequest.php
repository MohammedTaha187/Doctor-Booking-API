<?php

namespace App\Http\Requests\Api\V1\Payment;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'gateway' => 'required|in:stripe,paymob',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'appointment_id' => [
                'description' => 'The ID of the appointment to pay for.',
                'example' => 1,
            ],
            'gateway' => [
                'description' => 'The payment gateway to use.',
                'example' => 'stripe',
            ],
        ];
    }
}
