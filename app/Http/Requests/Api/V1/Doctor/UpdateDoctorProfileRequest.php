<?php

namespace App\Http\Requests\Api\V1\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->user()?->doctor?->id;

        return [
            'specialty_id' => 'sometimes|exists:specialties,id',
            'license_number' => 'sometimes|string|unique:doctors,license_number,'.$doctorId,
            'years_experience' => 'sometimes|integer|min:0',
            'consultation_fee' => 'sometimes|numeric|min:0',
            'consultation_type' => 'sometimes|in:online,in_person,both',
            'is_available' => 'sometimes|boolean',
            'translations' => 'sometimes|array',
            'translations.*' => 'array',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'specialty_id' => [
                'description' => 'The ID of the medical specialty.',
                'example' => 1,
            ],
            'license_number' => [
                'description' => 'Medical license number.',
                'example' => 'DOC987654',
            ],
            'years_experience' => [
                'description' => 'Years of experience.',
                'example' => 12,
            ],
            'consultation_fee' => [
                'description' => 'Fee per consultation.',
                'example' => 200.00,
            ],
            'consultation_type' => [
                'description' => 'Type of consultation offered.',
                'example' => 'online',
            ],
            'is_available' => [
                'description' => 'Is the doctor available for bookings.',
                'example' => true,
            ],
            'translations' => [
                'description' => 'Translations for bio, etc.',
                'example' => [
                    [
                        'locale' => 'en',
                        'field' => 'bio',
                        'value' => 'Experienced cardiologist.',
                    ],
                ],
            ],
        ];
    }
}
