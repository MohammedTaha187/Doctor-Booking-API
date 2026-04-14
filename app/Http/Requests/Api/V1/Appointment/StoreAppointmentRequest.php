<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'payment_id' => 'nullable|exists:payments,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
            'type' => 'required|in:online,in_person',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'meeting_link' => 'nullable|string',
        ];
    }
}
