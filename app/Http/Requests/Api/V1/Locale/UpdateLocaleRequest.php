<?php

namespace App\Http\Requests\Api\V1\Locale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:5|unique:locales,code,'.$this->route('locale'),
            'name' => 'sometimes|string|max:255',
            'native_name' => 'sometimes|string|max:255',
            'direction' => 'sometimes|in:ltr,rtl',
            'is_active' => 'boolean',
        ];
    }
}
