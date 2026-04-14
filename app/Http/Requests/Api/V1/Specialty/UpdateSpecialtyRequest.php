<?php

namespace App\Http\Requests\Api\V1\Specialty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => 'sometimes|string|max:255|unique:specialties,slug,'.$this->route('specialty'),
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'translations' => 'sometimes|array',
            'translations.*' => 'array',
        ];
    }
}
