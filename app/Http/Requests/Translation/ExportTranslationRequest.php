<?php

namespace App\Http\Requests\Translation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExportTranslationRequest extends FormRequest
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
            'locale' => ['required', 'string', 'exists:locales,code'],
            'tag' => ['nullable', 'string', 'exists:tags,name'],
        ];
    }

    public function getExportParams(): array
    {
        return [
            'locale' => $this->input('locale'),
            'tag' => $this->input('tag'),
        ];
    }
}
