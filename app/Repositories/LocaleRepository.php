<?php

namespace App\Repositories;

use App\Models\Locale;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocaleRepository
{
    public function findIdByCode(string $code): int
    {
        $localeId = Locale::where('code', $code)->value('id');

        if (!$localeId) {
            throw new ModelNotFoundException();
        }

        return $localeId;
    }
}
