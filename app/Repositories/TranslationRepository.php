<?php

namespace App\Repositories;

use App\DTO\Translation\TranslationParams;
use App\Models\Translation;
use App\DTO\Translation\SearchTranslationParams;
use App\DTO\PagingParams;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationRepository
{
    public function findById(int $id): Translation
    {
        return Translation::findOrFail($id);
    }

    public function findTranslations(SearchTranslationParams $params, PagingParams $pagingParams): LengthAwarePaginator
    {
        $query = Translation::query();

        $this->applyWithEagerLoad($query);
        $this->applySearchCondition($query, $params);
        $this->applySorting($query, $pagingParams);

        return $query->paginate($pagingParams->perPage, page: $pagingParams->page);
    }

    public function store(TranslationParams $params): Translation
    {
        $translation = Translation::create([
            'locale_id' => $params->localeId,
            'key' => $params->key,
            'value' => $params->value
        ]);

        return $translation;
    }

    public function update(Translation $translation, TranslationParams $params): Translation
    {
        $translation->update([
            'locale_id' => $params->localeId,
            'key' => $params->key,
            'value' => $params->value
        ]);

        return $translation;
    }

    public function delete(Translation $translation): void
    {
        $translation->delete();
    }

    public function exportByLocaleAndTag(int $localeId, ?string $tag): Collection
    {
        $query = Translation::query()
            ->select([
                'translations.key',
                'translations.value',
            ])
            ->where('translations.locale_id', $localeId)
            ->when(filled($tag), function ($query) use ($tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tags.name', $tag);
                });
            });

        return $query->pluck('value', 'key');
    }

    private function applyWithEagerLoad(Builder $query): void
    {
        $query->with(['locale', 'tags']);
    }

    private function applySearchCondition(Builder $query, SearchTranslationParams $params): Builder
    {
        return $query
            ->when(filled($params->key), function ($query) use ($params) {
                $query->where('key', 'like', "%{$params->key}%");
            })
            ->when(filled($params->value), function ($query) use ($params) {
                $query->where('value', 'like', "%{$params->value}%");
            })
            ->when(filled($params->tags), function ($query) use ($params) {
                $query->whereHas('tags', function ($query) use ($params) {
                    $query->whereIn('tags.id', $params->tags);
                });
            });
    }

    private function applySorting(Builder $query, PagingParams $params): Builder
    {
        return $query->orderBy($params->sortBy, $params->sortOrder);
    }
}
