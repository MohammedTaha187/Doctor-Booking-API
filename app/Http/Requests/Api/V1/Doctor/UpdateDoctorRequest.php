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
            'consultation_type' => 'required|in:online,offline',
            'rating' => 'required|numeric',
            'reviews_count' => 'required|integer',
            'is_verified' => 'required|boolean',
            'is_available' => 'required|boolean',
        ];
    }
}
