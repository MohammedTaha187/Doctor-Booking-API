<?php

namespace App\Http\Resources\Api\V1\Specialty;

use App\Models\Translation;
use App\Services\Api\V1\Translation\TranslationService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class SpecialtyResource extends JsonResource
{
    public function toArray($request): array
    {
        $translationService = app(TranslationService::class);

        return [
            'id' => $this->id,
            'name' => $this->relationLoaded('translations')
                ? $this->translations
                    ->first(fn (Translation $translation): bool => $translation->locale === App::getLocale() && $translation->field === 'name')
                    ?->value ?? $translationService->get($this->resource, 'name')
                : $translationService->get($this->resource, 'name'),
            'slug' => $this->slug,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
