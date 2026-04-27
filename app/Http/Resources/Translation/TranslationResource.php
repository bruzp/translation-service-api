<?php

namespace App\Http\Resources\Translation;

use App\Http\Resources\Tag\TagResourceCollection;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(Translation $resource)
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
            'localeId' => $this->whenLoaded('locale', function () {
                return $this->resource->locale->code;
            }),
            'key' => $this->resource->key,
            'value' => $this->resource->value,
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
            'tags' => $this->whenLoaded('tags', function () {
                return TagResourceCollection::make($this->resource->tags);
            }),
        ];
    }
}
