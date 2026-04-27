<?php

namespace App\Http\Requests\Translation;

use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
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
            'localeId' => ['required', 'exists:locales,id'],
            'key' => [
                'required',
                'string',
                Rule::unique('translations', 'key')
                    ->where('locale_id', $this->input('localeId')),
            ],
            'value' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'int', Rule::exists('tags', 'id')],
        ];
    }
}
