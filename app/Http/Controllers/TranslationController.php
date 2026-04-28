<?php

namespace App\Http\Controllers;

use App\Http\Requests\Translation\ExportTranslationRequest;
use App\Http\Requests\Translation\SearchTranslationRequest;
use App\Http\Requests\Translation\StoreTranslationRequest;
use App\Http\Requests\Translation\UpdateTranslationRequest;
use App\Http\Resources\Translation\TranslationResource;
use App\Http\Resources\Translation\TranslationResourceCollection;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TranslationController extends Controller
{
    public function __construct(private readonly TranslationService $translationService)
    {
    }

    public function index(SearchTranslationRequest $request): TranslationResourceCollection
    {
        $translations = $this->translationService->searchTranslations(
            $request->getSearchParams(),
            $request->getPaginatorConfig()
        );

        return new TranslationResourceCollection($translations);
    }

    public function show(int $id): TranslationResource
    {
        $translation = $this->translationService->getById($id);

        return new TranslationResource($translation);
    }

    public function store(StoreTranslationRequest $request): TranslationResource
    {
        $translation = $this->translationService->storeTranslation($request->getTranslationParams());

        return new TranslationResource($translation);
    }

    public function update(int $id, UpdateTranslationRequest $request): TranslationResource
    {
        $translation = $this->translationService->updateTranslation(
            $id,
            $request->getTranslationParams()
        );

        return new TranslationResource($translation);
    }

    public function destroy(int $id): Response
    {
        $this->translationService->deleteTranslation($id);

        return response()->noContent();
    }

    public function export(ExportTranslationRequest $request): JsonResponse
    {
        $data = $request->getExportParams();

        $translations = $this->translationService->exportTranslations(
            $data['locale'],
            $data['tag']
        );

        return response()->json($translations);
    }
}
