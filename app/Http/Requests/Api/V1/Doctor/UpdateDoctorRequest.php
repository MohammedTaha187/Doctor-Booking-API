<?php

namespace App\Http\Requests\Api\V1\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
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
                'description' => 'The ID of the user associated with this doctor.',
                'example' => 1,
            ],
            'specialty_id' => [
                'description' => 'The ID of the doctor\'s specialty.',
                'example' => 1,
            ],
            'license_number' => [
                'description' => 'The medical license number of the doctor.',
                'example' => 'DOC123456',
            ],
            'years_experience' => [
                'description' => 'Years of medical experience.',
                'example' => 10,
            ],
            'consultation_fee' => [
                'description' => 'Fee for a single consultation.',
                'example' => 150.00,
            ],
            'consultation_type' => [
                'description' => 'How the doctor conducts consultations.',
                'example' => 'both',
            ],
            'rating' => [
                'description' => 'Average rating of the doctor.',
                'example' => 4.8,
            ],
            'reviews_count' => [
                'description' => 'Total number of reviews for the doctor.',
                'example' => 25,
            ],
            'is_verified' => [
                'description' => 'Status of the doctor\'s verification.',
                'example' => true,
            ],
            'is_available' => [
                'description' => 'Availability status for new appointments.',
                'example' => true,
            ],
        ];
    }
}
