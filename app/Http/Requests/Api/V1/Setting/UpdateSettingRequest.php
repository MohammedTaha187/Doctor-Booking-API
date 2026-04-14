<?php

namespace App\Http\Requests\Api\V1\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|string|max:255|unique:settings,key,'.$this->route('setting'),
            'value' => 'nullable|string',
            'group' => 'nullable|string|max:255',
            'type' => 'sometimes|in:text,number,boolean,json',
        ];
    }
}
