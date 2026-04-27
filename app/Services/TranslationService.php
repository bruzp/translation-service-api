<?php

namespace App\Services;

use App\DTO\PagingParams;
use App\DTO\Translation\SearchTranslationParams;
use App\DTO\Translation\TranslationParams;
use App\Models\Translation;
use App\Repositories\TagRepository;
use App\Repositories\TranslationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly TagRepository $tagRepository
    ) {
    }

    public function searchTranslations(SearchTranslationParams $params, PagingParams $pagingParams): LengthAwarePaginator
    {
        return $this->translationRepository->findTranslations($params, $pagingParams);
    }

    public function getById(int $id): Translation
    {
        $translation = $this->translationRepository->findById($id);

        return $translation->load('locale', 'tags');
    }

    public function storeTranslation(TranslationParams $params)
    {
        return DB::transaction(function () use ($params) {
            $translation = $this->translationRepository->store($params);

            if (filled($params->tags)) {
                $this->tagRepository->syncTags($translation, $params->tags);
            }

            return $translation->load('locale', 'tags');
        });
    }

    public function updateTranslation(int $id, TranslationParams $params)
    {
        $translation = $this->translationRepository->findById($id);

        return DB::transaction(function () use ($translation, $params) {
            $translation = $this->translationRepository->update($translation, $params);

            if (filled($params->tags)) {
                $this->tagRepository->syncTags($translation, $params->tags);
            }

            return $translation->load('locale', 'tags');
        });
    }

    public function deleteTranslation(int $id): void
    {
        $translation = $this->translationRepository->findById($id);

        DB::transaction(function () use ($translation) {
            $this->tagRepository->detachTags($translation);

            $this->translationRepository->delete($translation);
        });
    }
}
