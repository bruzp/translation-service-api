<?php

namespace App\Services;

use App\Repositories\TagRepository;
use App\Repositories\TranslationRepository;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly TagRepository $tagRepository
    ) {
    }

    public function storeTranslation(int $localeId, string $key, string $value, array $tags = [])
    {
        return DB::transaction(function () use ($localeId, $key, $value, $tags) {
            $translation = $this->translationRepository->store($localeId, $key, $value);

            if (filled($tags)) {
                $this->tagRepository->syncTags($translation, $tags);
            }

            return $translation->load('locale', 'tags');
        });
    }
}
