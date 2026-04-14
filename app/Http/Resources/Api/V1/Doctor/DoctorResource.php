<?php

namespace App\Http\Resources\Api\V1\Doctor;

use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Http\Resources\Api\V1\Specialty\SpecialtyResource;
use App\Models\Translation;
use App\Services\Api\V1\Translation\TranslationService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class DoctorResource extends JsonResource
{
    public function toArray($request): array
    {
        $translationService = app(TranslationService::class);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'specialty_id' => $this->specialty_id,
            'specialty' => new SpecialtyResource($this->whenLoaded('specialty')),
            'bio' => $this->relationLoaded('translations')
                ? $this->translations
                    ->first(fn (Translation $translation): bool => $translation->locale === App::getLocale() && $translation->field === 'bio')
                    ?->value ?? $translationService->get($this->resource, 'bio')
                : $translationService->get($this->resource, 'bio'),
            'license_number' => $this->license_number,
            'years_experience' => $this->years_experience,
            'consultation_fee' => $this->consultation_fee,
            'consultation_type' => $this->consultation_type,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'is_verified' => $this->is_verified,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
        ];
    }
}
