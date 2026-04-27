<?php

namespace App\DTO\Translation;

readonly class SearchTranslationParams
{
    public function __construct(
        public ?string $key,
        public ?string $value,
        public ?array $tags,
    ) {
    }
}
