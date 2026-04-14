<?php

namespace App\Http\Resources\Api\V1\Translation;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'translatable_type' => $this->translatable_type,
            'translatable_id' => $this->translatable_id,
            'locale' => $this->locale,
            'field' => $this->field,
            'value' => $this->value,
            'created_at' => $this->created_at,
        ];
    }
}
