<?php

namespace App\Repositories;

use App\Models\Translation;

class TagRepository
{
    public function syncTags(Translation $translation, array $tags): void
    {
        $translation->tags()->sync($tags);
    }
}
