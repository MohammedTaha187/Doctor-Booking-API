<?php

namespace App\Services\Api\V1\Translation;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TranslationService
{
    /**
     * Request-local cache for translation lookups.
     *
     * @var array<string, string>
     */
    private array $cache = [];

    /**
     * Sync translations for a given model.
     * $translations = ['ar' => ['name' => 'جراحة'], 'en' => ['name' => 'Surgery']]
     */
    public function sync(Model $model, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                Translation::updateOrCreate(
                    [
                        'translatable_type' => get_class($model),
                        'translatable_id' => $model->id,
                        'locale' => $locale,
                        'field' => $field,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }
    }

    /**
     * Get translation for a specific field in the current locale.
     */
    public function get(Model $model, string $field): ?string
    {
        $locale = App::getLocale();
        $cacheKey = get_class($model).'|'.$model->id.'|'.$locale.'|'.$field;

        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        return $this->cache[$cacheKey] = Translation::where([
            'translatable_type' => get_class($model),
            'translatable_id' => $model->id,
            'locale' => $locale,
            'field' => $field,
        ])->value('value') ?? "[{$field}:{$locale}]";
    }
}
