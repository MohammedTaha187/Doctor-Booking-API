<?php

namespace App\Http\Resources\Api\V1\Specialty;

use App\Services\Api\V1\Translation\TranslationService;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialtyResource extends JsonResource
{
    public function toArray($request): array
    {
        $translationService = app(TranslationService::class);

        return [
            'id' => $this->id,
            'name' => $translationService->get($this->resource, 'name'),
            'slug' => $this->slug,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
