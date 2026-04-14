<?php

namespace App\Http\Requests\Api\V1\Translation;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translatable_type' => 'required|string|max:255',
            'translatable_id' => 'required|uuid',
            'locale' => 'required|string|size:5',
            'field' => 'required|string|max:255',
            'value' => 'required|string',
        ];
    }
}
