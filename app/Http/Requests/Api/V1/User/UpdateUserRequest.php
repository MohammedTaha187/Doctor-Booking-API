<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$this->route('user'),
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'gender' => 'sometimes|in:male,female',
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
                'description' => 'The email address of the user.',
                'example' => 'john.updated@example.com',
            ],
            'password' => [
                'description' => 'New password (optional, minimum 6 characters).',
                'example' => 'newpassword123',
            ],
            'avatar' => [
                'description' => 'URL or path to the user\'s avatar image.',
                'example' => 'https://example.com/avatars/john_new.jpg',
            ],
            'phone' => [
                'description' => 'Working phone number of the user.',
                'example' => '+1987654321',
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
                'example' => 'ar',
            ],
        ];
    }
}
