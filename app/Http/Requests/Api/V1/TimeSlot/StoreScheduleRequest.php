<?php

namespace App\Http\Requests\Api\V1\TimeSlot;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'required|integer|min:10|max:120',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'day_of_week' => [
                'description' => 'The day of the week for the schedule.',
                'example' => 'monday',
            ],
            'start_time' => [
                'description' => 'Start time of the working hours (H:i).',
                'example' => '09:00',
            ],
            'end_time' => [
                'description' => 'End time of the working hours (H:i).',
                'example' => '17:00',
            ],
            'duration_minutes' => [
                'description' => 'Duration of each appointment slot in minutes.',
                'example' => 30,
            ],
        ];
    }
}
