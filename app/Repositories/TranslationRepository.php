<?php

namespace App\Repositories;

use App\Models\Translation;

class TranslationRepository
{
    public function store(int $localeId, string $key, string $value)
    {
        $translation = Translation::create([
            'locale_id' => $localeId,
            'key' => $key,
            'value' => $value
        ]);

        return $translation;
    }
}
