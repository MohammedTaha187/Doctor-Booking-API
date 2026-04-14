<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'sometimes|exists:users,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
            'time_slot_id' => 'sometimes|exists:time_slots,id',
            'payment_id' => 'nullable|exists:payments,id',
            'scheduled_date' => 'sometimes|date',
            'scheduled_time' => 'sometimes|date_format:H:i',
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed,no_show',
            'type' => 'sometimes|in:online,in_person',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
            'payment_status' => 'sometimes|in:unpaid,paid,refunded',
            'meeting_link' => 'nullable|string',
        ];
    }
}
