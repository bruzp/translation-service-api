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
use OpenApi\Attributes as OA;

class TranslationController extends Controller
{
    public function __construct(private readonly TranslationService $translationService)
    {
    }

    #[OA\Get(
        path: '/translations',
        tags: ['Translations'],
        summary: 'List translations',
        security: [['sanctum' => []]],
        parameters: [
        new OA\Parameter(name: 'key', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'value', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(
            name: 'tags[]',
            in: 'query',
            schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer'))
        ),
        new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', example: 1)),
        new OA\Parameter(name: 'perPage', in: 'query', schema: new OA\Schema(type: 'integer', example: 15)),
        new OA\Parameter(name: 'sortBy', in: 'query', schema: new OA\Schema(type: 'string', example: 'created_at')),
        new OA\Parameter(name: 'sortOrder', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
    ],
        responses: [
        new OA\Response(
            response: 200,
            description: 'Translation list',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/TranslationResponse')
            )
        ),
        new OA\Response(response: 401, description: 'Unauthenticated'),
    ]
    )]
    public function index(SearchTranslationRequest $request): TranslationResourceCollection
    {
        $translations = $this->translationService->searchTranslations(
            $request->getSearchParams(),
            $request->getPaginatorConfig()
        );

        return new TranslationResourceCollection($translations);
    }

    #[OA\Get(
        path: '/translations/{id}',
        tags: ['Translations'],
        summary: 'Get translation',
        security: [['sanctum' => []]],
        parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
        responses: [
        new OA\Response(
            response: 200,
            description: 'Translation details',
            content: new OA\JsonContent(ref: '#/components/schemas/TranslationResponse')
        ),
        new OA\Response(response: 401, description: 'Unauthenticated'),
        new OA\Response(response: 404, description: 'Not found'),
    ]
    )]
    public function show(int $id): TranslationResource
    {
        $translation = $this->translationService->getById($id);

        return new TranslationResource($translation);
    }

    #[OA\Post(
        path: '/translations',
        tags: ['Translations'],
        summary: 'Create translation',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TranslationRequest')
        ),
        responses: [
        new OA\Response(
            response: 201,
            description: 'Created',
            content: new OA\JsonContent(ref: '#/components/schemas/TranslationResponse')
        ),
        new OA\Response(response: 401, description: 'Unauthenticated'),
        new OA\Response(response: 422, description: 'Validation error'),
    ]
    )]
    public function store(StoreTranslationRequest $request): TranslationResource
    {
        $translation = $this->translationService->storeTranslation($request->getTranslationParams());

        return new TranslationResource($translation);
    }

    #[OA\Put(
        path: '/translations/{id}',
        tags: ['Translations'],
        summary: 'Update translation',
        security: [['sanctum' => []]],
        parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TranslationRequest')
        ),
        responses: [
        new OA\Response(
            response: 200,
            description: 'Updated',
            content: new OA\JsonContent(ref: '#/components/schemas/TranslationResponse')
        ),
        new OA\Response(response: 401, description: 'Unauthenticated'),
        new OA\Response(response: 404, description: 'Not found'),
        new OA\Response(response: 422, description: 'Validation error'),
    ]
    )]
    public function update(int $id, UpdateTranslationRequest $request): TranslationResource
    {
        $translation = $this->translationService->updateTranslation(
            $id,
            $request->getTranslationParams()
        );

        return new TranslationResource($translation);
    }

    #[OA\Delete(
        path: '/translations/{id}',
        tags: ['Translations'],
        summary: 'Delete translation',
        security: [['sanctum' => []]],
        parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
        responses: [
        new OA\Response(response: 204, description: 'Deleted'),
        new OA\Response(response: 401, description: 'Unauthenticated'),
        new OA\Response(response: 404, description: 'Not found'),
    ]
    )]
    public function destroy(int $id): Response
    {
        $this->translationService->deleteTranslation($id);

        return response()->noContent();
    }

    #[OA\Get(
        path: '/translations/export',
        tags: ['Translations'],
        summary: 'Export translations',
        parameters: [
        new OA\Parameter(name: 'locale', in: 'query', required: true, schema: new OA\Schema(type: 'string', example: 'en')),
        new OA\Parameter(name: 'tag', in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'web')),
    ],
        responses: [
        new OA\Response(response: 200, description: 'Exported translations'),
        new OA\Response(response: 304, description: 'Not modified'),
        new OA\Response(response: 422, description: 'Validation error'),
    ]
    )]
    public function export(ExportTranslationRequest $request): JsonResponse
    {
        $data = $request->getExportParams();

        $translations = $this->translationService->exportTranslations(
            $data['locale'],
            $data['tag']
        );

        $etag = '"' . md5(json_encode($translations)) . '"';

        if ($request->header('If-None-Match') === $etag) {
            return response()
                ->json(null, 304)
                ->header('Cache-Control', 'public, max-age=0, must-revalidate')
                ->header('ETag', $etag);
        }

        return response()
            ->json($translations)
            ->header('Cache-Control', 'public, max-age=0, must-revalidate')
            ->header('ETag', $etag);
    }
}
