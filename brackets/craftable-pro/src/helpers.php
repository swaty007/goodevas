<?php

use App\Settings\GeneralSettings;

if (! function_exists("___")) {
    /**
     * @param $group
     * @param $key
     * @param $params
     * @param $locale
     * @return array|\Illuminate\Contracts\Translation\Translator|\Illuminate\Foundation\Application|string
     */
    function ___($group, $key, $params = [], $locale = null)
    {
        return trans($group . '.' . $key, $params, $locale);
    }

    /**
     * @param $group
     * @param $key
     * @param $number
     * @param $params
     * @param $locale
     * @return string
     */
    function ___ch($group, $key, $number, $params = [], $locale = null)
    {
        return trans_choice($group . '.' . $key, $number, $params, $locale);
    }
}

if (! function_exists('getAvailableLocalesTranslated')) {
    /**
     * @return array
     */
    function getAvailableLocalesTranslated()
    {
        return collect(app(GeneralSettings::class)->available_locales)->map(function ($locale) {
            return [
                "key" => $locale,
                "value" => trans("locales.craftable-pro.$locale"),
            ];
        })->all();
    }
}
