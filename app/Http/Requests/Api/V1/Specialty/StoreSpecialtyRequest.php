<?php

namespace App\Http\Requests\Api\V1\Specialty;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|string|max:255|unique:specialties,slug',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*' => 'array',
        ];
    }
}
