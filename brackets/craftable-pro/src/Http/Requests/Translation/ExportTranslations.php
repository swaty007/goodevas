<?php

namespace Brackets\CraftablePro\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ExportTranslations extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.translation.export');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['exportLanguages' => 'array|required'];
    }
}
