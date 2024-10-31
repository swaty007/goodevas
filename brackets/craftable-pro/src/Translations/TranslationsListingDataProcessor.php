<?php

namespace Brackets\CraftablePro\Translations;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Translations\Repositories\LanguageLineRepository;
use Illuminate\Contracts\Translation\Translator;

class TranslationsListingDataProcessor
{
    public function __construct(private LanguageLineRepository $languageLineRepository)
    {
    }

    /**
     * @param $data
     * @return array
     */
    public function getProcessedData($data): array
    {
        $locales = collect(app(GeneralSettings::class)->available_locales);

        $data->map(function ($translation) use ($locales) {
            $locales->each(function ($locale) use ($translation) {
                /** @var LanguageLine $translation */
                $translation->setTranslation($locale, $this->getCurrentTransForTranslation($translation, $locale));
            });

            return $translation->getTranslation(app()->getLocale());
        });

        return ([
            'data' => $data,
            'locales' => $locales,
            'groups' => $this->languageLineRepository->getGroups(),
        ]);
    }

    /**
     * @param LanguageLine $translation
     * @param $locale
     * @return array|Translator|string|null
     */
    private function getCurrentTransForTranslation(LanguageLine $translation, $locale)
    {
        return $translation->text[$locale] ?? $translation->key;
    }
}
