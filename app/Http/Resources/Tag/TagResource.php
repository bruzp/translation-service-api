<?php

namespace App\Http\Resources\Tag;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(Tag $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
