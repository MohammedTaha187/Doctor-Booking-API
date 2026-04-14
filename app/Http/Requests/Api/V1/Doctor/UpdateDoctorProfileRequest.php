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
        return [
            'specialty_id' => 'sometimes|exists:specialties,id',
            'license_number' => 'sometimes|string|unique:doctors,license_number,'.optional($this->user()->doctor)->id,
            'years_experience' => 'sometimes|integer|min:0',
            'consultation_fee' => 'sometimes|numeric|min:0',
            'consultation_type' => 'sometimes|in:online,in_person,both',
            'is_available' => 'sometimes|boolean',
            'translations' => 'sometimes|array',
            'translations.*' => 'array',
        ];
    }
}
