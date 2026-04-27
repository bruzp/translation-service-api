<?php

namespace App\DTO\Translation;

readonly class TranslationParams
{
    public function __construct(
        public int $localeId,
        public string $key,
        public string $value,
        public ?array $tags,
    ) {
    }
}
