<?php

namespace App\Http\Resources\Api\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'group' => $this->group,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
