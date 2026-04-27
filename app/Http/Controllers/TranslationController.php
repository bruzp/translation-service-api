<?php

namespace App\Http\Controllers;

use App\Http\Requests\Translation\StoreTranslationRequest;
use App\Http\Resources\Translation\TranslationResource;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    public function __construct(private readonly TranslationService $translationService)
    {
    }

    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();

        $translation = $this->translationService->storeTranslation(
            $data['localeId'],
            $data['key'],
            $data['value'],
            $data['tags'] ?? []
        );

        return new TranslationResource($translation);
    }
}
