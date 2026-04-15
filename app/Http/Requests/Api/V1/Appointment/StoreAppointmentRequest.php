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

    public function bodyParameters(): array
    {
        return [
            'patient_id' => [
                'description' => 'The ID of the patient.',
                'example' => 1,
            ],
            'doctor_id' => [
                'description' => 'The ID of the doctor.',
                'example' => 2,
            ],
            'time_slot_id' => [
                'description' => 'The ID of the specific time slot.',
                'example' => 5,
            ],
            'payment_id' => [
                'description' => 'The ID of the payment record.',
                'example' => null,
            ],
            'scheduled_date' => [
                'description' => 'The date of the appointment.',
                'example' => '2026-05-20',
            ],
            'scheduled_time' => [
                'description' => 'The start time of the appointment.',
                'example' => '10:30',
            ],
            'status' => [
                'description' => 'Current status of the appointment.',
                'example' => 'pending',
            ],
            'type' => [
                'description' => 'Type of the appointment.',
                'example' => 'online',
            ],
            'notes' => [
                'description' => 'Optional notes about the appointment.',
                'example' => 'Follow-up checkup.',
            ],
            'cancellation_reason' => [
                'description' => 'Reason for cancellation if applicable.',
                'example' => null,
            ],
            'payment_status' => [
                'description' => 'Status of the appointment payment.',
                'example' => 'unpaid',
            ],
            'meeting_link' => [
                'description' => 'Link for the online meeting.',
                'example' => 'https://zoom.us/j/123456789',
            ],
        ];
    }
}
