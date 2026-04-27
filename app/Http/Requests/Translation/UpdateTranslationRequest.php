<?php

namespace App\Http\Requests\Translation;

use App\DTO\Translation\TranslationParams;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTranslationRequest extends FormRequest
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
                    ->where('locale_id', $this->input('localeId'))
                    ->ignore($this->route('translation')),
            ],
            'value' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'int', Rule::exists('tags', 'id')],
        ];
    }

    public function getTranslationParams(): TranslationParams
    {
        return new TranslationParams(
            $this->validated('localeId'),
            $this->validated('key'),
            $this->validated('value'),
            $this->validated('tags'),
        );
    }
}
