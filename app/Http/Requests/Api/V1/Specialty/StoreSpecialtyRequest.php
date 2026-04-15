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

    public function bodyParameters(): array
    {
        return [
            'slug' => [
                'description' => 'The unique URL-friendly slug for the specialty.',
                'example' => 'cardiology',
            ],
            'icon' => [
                'description' => 'Icon class or URL representing the specialty.',
                'example' => 'icon-heart',
            ],
            'is_active' => [
                'description' => 'Whether the specialty is active and visible.',
                'example' => true,
            ],
            'translations' => [
                'description' => 'An array of translations for the specialty name and description.',
                'example' => [
                    [
                        'locale' => 'en',
                        'name' => 'Cardiology',
                        'description' => 'Heart related treatments.',
                    ],
                ],
            ],
        ];
    }
}
