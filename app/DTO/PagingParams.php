<?php

namespace App\DTO;

readonly class PagingParams
{
    public function __construct(
        public ?int $page = 1,
        public ?int $perPage = 20,
        public ?string $sortBy = "created_at",
        public ?string $sortOrder = "desc",
    ) {
    }
}
