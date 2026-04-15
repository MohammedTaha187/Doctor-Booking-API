<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'language_preference' => 'nullable|string|max:2',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The full name of the user.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'The email address of the user (must be unique).',
                'example' => 'john.doe@example.com',
            ],
            'password' => [
                'description' => 'The user\'s account password (minimum 6 characters).',
                'example' => 'password123',
            ],
            'avatar' => [
                'description' => 'URL or path to the user\'s avatar image.',
                'example' => 'https://example.com/avatars/john.jpg',
            ],
            'phone' => [
                'description' => 'Working phone number of the user.',
                'example' => '+1234567890',
            ],
            'gender' => [
                'description' => 'The gender of the user.',
                'example' => 'male',
            ],
            'date_of_birth' => [
                'description' => 'User\'s date of birth.',
                'example' => '1990-01-01',
            ],
            'language_preference' => [
                'description' => 'Preferred language code (e.g., en, ar).',
                'example' => 'en',
            ],
        ];
    }
}
