<?php

namespace App\Http\Requests\Api\V1\Translation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translatable_type' => 'sometimes|string|max:255',
            'translatable_id' => 'sometimes|uuid',
            'locale' => 'sometimes|string|size:5',
            'field' => 'sometimes|string|max:255',
            'value' => 'sometimes|string',
        ];
    }
}
