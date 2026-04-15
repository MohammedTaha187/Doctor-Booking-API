<?php

namespace App\Http\Requests\Api\V1\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'specialty_id' => 'required|exists:specialties,id',
            'license_number' => 'required|string|max:255',
            'years_experience' => 'required|integer',
            'consultation_fee' => 'required|numeric',
            'consultation_type' => 'required|in:online,in_person,both',
            'rating' => 'required|numeric',
            'reviews_count' => 'required|integer',
            'is_verified' => 'required|boolean',
            'is_available' => 'required|boolean',

        ];
    }

    public function bodyParameters(): array
    {
        return [
            'user_id' => [
                'description' => 'The ID of the user to be assigned as a doctor.',
                'example' => 1,
            ],
            'specialty_id' => [
                'description' => 'The ID of the medical specialty.',
                'example' => 1,
            ],
            'license_number' => [
                'description' => 'The medical license number of the doctor.',
                'example' => 'DOC123456',
            ],
            'years_experience' => [
                'description' => 'Years of professional experience.',
                'example' => 10,
            ],
            'consultation_fee' => [
                'description' => 'Fee per consultation.',
                'example' => 150.00,
            ],
            'consultation_type' => [
                'description' => 'Type of consultation offered.',
                'example' => 'both',
            ],
            'rating' => [
                'description' => 'Initial rating of the doctor.',
                'example' => 4.5,
            ],
            'reviews_count' => [
                'description' => 'Number of reviews.',
                'example' => 0,
            ],
            'is_verified' => [
                'description' => 'Whether the doctor profile is verified.',
                'example' => true,
            ],
            'is_available' => [
                'description' => 'Whether the doctor is currently available for bookings.',
                'example' => true,
            ],
        ];
    }
}
