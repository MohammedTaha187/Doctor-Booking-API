<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;

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
            'time_slot_id' => 'required|exists:time_slots,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:online,in_person',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
