<?php

namespace App\Http\Requests\Api\V1\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'appointment_id' => 'sometimes|exists:appointments,id',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_approved' => 'boolean',
        ];
    }
}
