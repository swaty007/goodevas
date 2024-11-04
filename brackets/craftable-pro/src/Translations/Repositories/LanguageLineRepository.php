<?php

namespace Brackets\CraftablePro\Translations\Repositories;

use Brackets\CraftablePro\Translations\LanguageLine;
use Illuminate\Support\Collection;

class LanguageLineRepository
{
    /**
     * @param  null  $language
     * @param  null  $text
     */
    public function createLanguageLineIfDoesntExist($group, $key, $language = null, $text = null): ?LanguageLine
    {
        if (empty(trim($key))) {
            return null;
        }

        try {
            /** @var LanguageLine $translation */
            $languageLine = LanguageLine::withTrashed()
                ->where('group', $group)
                ->where('key', $key)
                ->first();

            $defaultLocale = config('app.locale');

            // because Laravel & MySQL are case-insensitive by default, let's double check we have the right $languageLine
            if ($languageLine && $languageLine->key === $key) {
                $languageLine->restore();
            } else {
                $languageLine = LanguageLine::make([
                    'group' => $group,
                    'key' => $key,
                    'text' => ($text && $language) ? [$language => $text] : [],
                ]);
                $n = 0;
                loop:
                try {
                    $languageLine->save();
                    //                $languageLine = LanguageLine::withTrashed()->updateOrCreate([
                    //                    'group' => $group,
                    //                    'key' => $key,
                    //                ], [
                    //                    'text' => ($text && $language) ? [$language => $text] : []
                    //                ]);
                } catch (\Exception $e) {
                    if ($n >= 200) {
                        throw $e;
                    }
                    $n++;
                    goto loop;
                }
            }

            return $languageLine;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return Collection
     */
    public function getGroups()
    {
        return LanguageLine::query()
            ->groupBy('group')
            ->pluck('group');
    }

    public function deleteLanguageLines()
    {
        LanguageLine::query()->delete();
    }
}
