<?php

namespace Brackets\CraftablePro\Http\Requests\Translation;

use Brackets\CraftablePro\Translations\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTranslation extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.translation.edit', [$this->translation]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param mixed $locale
     * @return array
     */
    public function translatableRules($locale): array
    {
        return [
            'text' => 'string|nullable',
        ];
    }

    /**
     * @return string
     */
    public function getChosenLanguage(): string
    {
        return mb_strtolower($this->importLanguage);
    }

    /**
     * @return mixed
     */
    public function getResolvedConflicts()
    {
        return $this->resolvedTranslations;
    }
}
