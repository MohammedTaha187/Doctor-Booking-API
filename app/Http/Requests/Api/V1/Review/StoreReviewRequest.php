<?php

namespace App\Http\Requests\Api\V1\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'appointment_id' => [
                'description' => 'The ID of the completed appointment being reviewed.',
                'example' => 1,
            ],
            'rating' => [
                'description' => 'The rating score (1 to 5).',
                'example' => 5,
            ],
            'comment' => [
                'description' => 'The reviewer\'s textual feedback.',
                'example' => 'Excellent service and friendly doctor.',
            ],
        ];
    }
}
