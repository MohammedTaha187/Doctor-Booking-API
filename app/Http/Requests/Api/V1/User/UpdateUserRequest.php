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
}
