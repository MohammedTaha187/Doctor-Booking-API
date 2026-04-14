<?php

namespace App\Http\Resources\Api\V1\Appointment;

use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Http\Resources\Api\V1\Doctor\DoctorResource;
use App\Http\Resources\Api\V1\Payment\PaymentResource;
use App\Http\Resources\Api\V1\TimeSlot\TimeSlotResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'patient' => new UserResource($this->whenLoaded('patient')),
            'doctor_id' => $this->doctor_id,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'time_slot_id' => $this->time_slot_id,
            'time_slot' => new TimeSlotResource($this->whenLoaded('timeSlot')),
            'payment_id' => $this->payment_id,
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            'scheduled_date' => $this->scheduled_date,
            'scheduled_time' => $this->scheduled_time,
            'status' => $this->status,
            'type' => $this->type,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'payment_status' => $this->payment_status,
            'meeting_link' => $this->meeting_link,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
