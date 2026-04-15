<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:doctors,id',
            'time_slot_id' => [
                'required',
                Rule::exists('time_slots', 'id')->where(function ($query): void {
                    $query->where('doctor_id', $this->doctor_id);
                }),
            ],
            'scheduled_date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:online,in_person',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'doctor_id' => [
                'description' => 'The ID of the doctor being booked.',
                'example' => 1,
            ],
            'time_slot_id' => [
                'description' => 'The ID of the time slot for the appointment.',
                'example' => 10,
            ],
            'scheduled_date' => [
                'description' => 'The date for the appointment.',
                'example' => '2026-05-25',
            ],
            'type' => [
                'description' => 'The type of consultation required.',
                'example' => 'online',
            ],
            'notes' => [
                'description' => 'Optional instructions or context for the doctor.',
                'example' => 'I have a headache.',
            ],
        ];
    }
}
