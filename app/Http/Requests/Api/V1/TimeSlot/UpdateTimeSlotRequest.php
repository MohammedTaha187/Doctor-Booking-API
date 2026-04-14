<?php

namespace App\Http\Requests\Api\V1\TimeSlot;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'sometimes|exists:doctors,id',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'duration_minutes' => 'sometimes|integer',
            'is_available' => 'boolean',
        ];
    }
}
