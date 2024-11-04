<?php

namespace Brackets\CraftablePro\Http\Requests\Translation;

use Brackets\CraftablePro\Translations\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTranslation extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.translation.edit', [$this->translation]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param  mixed  $locale
     */
    public function translatableRules($locale): array
    {
        return [
            'text' => 'string|nullable',
        ];
    }

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
