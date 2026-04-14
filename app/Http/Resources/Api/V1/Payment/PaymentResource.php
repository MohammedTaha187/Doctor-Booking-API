<?php

namespace App\Http\Resources\Api\V1\Payment;

use App\Http\Resources\Api\V1\Appointment\AppointmentResource;
use App\Http\Resources\Api\V1\Auth\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'appointment_id' => $this->appointment_id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'gateway' => $this->gateway,
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'gateway_order_id' => $this->gateway_order_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
