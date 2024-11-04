<?php

namespace Brackets\CraftablePro\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImportTranslation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.translation.import');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'importLanguage' => 'string|required',
            'onlyMissing' => 'string',
            'fileImport' => 'required|file',
        ];
    }

    /**
     * @return string
     */
    public function getChosenLanguage()
    {
        return mb_strtolower($this->importLanguage);
    }
}
