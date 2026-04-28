<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
    ]
)]
#[OA\Schema(
    schema: 'LoginResponse',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|plain-text-token'),
    ]
)]
#[OA\Schema(
    schema: 'Tag',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'web'),
    ]
)]
#[OA\Schema(
    schema: 'TranslationResponse',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'localeId', type: 'string', example: 'en'),
        new OA\Property(property: 'key', type: 'string', example: 'auth.login'),
        new OA\Property(property: 'value', type: 'string', example: 'Login'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Tag'),
            example: [
                ['id' => 1, 'name' => 'web'],
                ['id' => 2, 'name' => 'mobile'],
                ['id' => 3, 'name' => 'desktop'],
            ]
        )
    ]
)]
#[OA\Schema(
    schema: 'TranslationRequest',
    required: ['localeId', 'key', 'value'],
    properties: [
        new OA\Property(property: 'localeId', type: 'integer', example: 1),
        new OA\Property(property: 'key', type: 'string', example: 'auth.login'),
        new OA\Property(property: 'value', type: 'string', example: 'Login'),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(type: 'integer'),
            example: [1, 2, 3]
        ),
    ]
)]
class Schemas
{
}
