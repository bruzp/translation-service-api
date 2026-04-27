<?php

namespace App\Http\Requests\Translation;

use App\DTO\PagingParams;
use App\DTO\Translation\SearchTranslationParams;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'string'],
            'value' => ['sometimes', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['nullable', 'int', Rule::exists('tags', 'id')],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
            'sortBy' => ['nullable', 'string'],
            'sortOrder' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function getSearchParams(): SearchTranslationParams
    {
        return new SearchTranslationParams(
            $this->validated('key'),
            $this->validated('value'),
            $this->validated('tags'),
        );
    }

    public function getPaginatorConfig(): PagingParams
    {
        return new PagingParams(
            $this->validated('page'),
            $this->validated('perPage'),
            $this->validated('sortBy'),
            $this->validated('sortOrder'),
        );
    }
}
