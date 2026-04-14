<?php

namespace App\Http\Resources\Api\V1\Locale;

use Illuminate\Http\Resources\Json\JsonResource;

class LocaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'native_name' => $this->native_name,
            'direction' => $this->direction,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
