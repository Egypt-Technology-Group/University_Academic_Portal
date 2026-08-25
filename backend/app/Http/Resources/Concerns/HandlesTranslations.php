<?php

namespace App\Http\Resources\Concerns;

use Illuminate\Http\Request;

trait HandlesTranslations
{
    /**
     * Format a translatable attribute according to the request locale or all_locales flag.
     */
    protected function translate(string $attribute, ?Request $request = null): mixed
    {
        $resource = $this->resource;

        if (!$resource) {
            return null;
        }

        $isTranslatable = method_exists($resource, 'getTranslations')
            && (
                (method_exists($resource, 'getTranslatableAttributes') && in_array($attribute, $resource->getTranslatableAttributes()))
                || (property_exists($resource, 'translatable') && in_array($attribute, $resource->translatable))
            );

        if (!$isTranslatable) {
            return $resource->{$attribute} ?? null;
        }

        $req = $request ?? request();

        $allLocalesRequested = ($req && ($req->boolean('all_locales')
            || $req->query('all_locales') !== null
            || $req->boolean('all_translations')
            || $req->query('all_translations') !== null
            || $req->hasHeader('X-All-Locales')
            || $req->query('locale') === 'all'));

        if ($allLocalesRequested) {
            return $resource->getTranslations($attribute);
        }

        $locale = app()->getLocale();
        $translation = $resource->getTranslation($attribute, $locale, false);

        if ($translation === null || $translation === '' || (is_array($translation) && empty($translation))) {
            $fallback = $locale === 'ar' ? 'en' : 'ar';
            $translation = $resource->getTranslation($attribute, $fallback, false);
        }

        return ($translation !== null && $translation !== '' && !(is_array($translation) && empty($translation)))
            ? $translation
            : $resource->{$attribute};
    }
}
