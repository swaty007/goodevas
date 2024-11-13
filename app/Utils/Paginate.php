<?php

namespace App\Utils;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class Paginate
{
    /**
     * @var null
     */
    protected static $defaultSortColumn = null;
    /**
     * @param array $items
     * @param int|string $perPage
     * @param int|null $page
     * @param array|null $searchSymbol
     * @param string|null $searchKey
     * @return LengthAwarePaginator
     */
    public static function paginate($items, int|string $perPage = 5, $page = null, $searchSymbol = null, $searchKey = null): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = collect($items);
        $total = $items->count();
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;

        $searchText = empty($searchSymbol['search']) ? null : $searchSymbol['search'];

        // Фильтрация по символу (если указан поисковый символ)
        if ($searchText && $searchKey) {
            $items = $items->filter(function ($item) use ($searchText, $searchKey) {
                $value = $item[$searchKey] ?? $item['id'] ?? null;
                if ($value) {
                    return strlen($searchText) > 3 ?
                        str_contains(mb_strtolower($value), mb_strtolower($searchText)) :
                        str_starts_with(mb_strtolower($value), mb_strtolower($searchText));
                }
                return false;
            });
            $total = count($items);
        }

        $filters = $searchSymbol;
        foreach ($filters as $key => $value) {
            $items = $items->filter(function ($item) use ($key, $value) {
                if (isset($item[$key])) {
                    if (is_array($value)) {
                        return in_array($item[$key], $value);
                    }
                    return $item[$key] == $value;
                }
                return true;
            });
            $total = count($items);
        }

        $sort = Paginator::resolveQueryString()['sort'] ?? self::$defaultSortColumn;
        // Сортировка
        if ((string)$sort) {
            $descending = $sort[0] === '-';
            $sort = ltrim($sort, '-');
            $items = $items->{$descending ? 'sortBy' : 'sortByDesc'}($sort);
        }

        // Обрезка результатов с учетом постраничного вывода
        $itemstoshow = array_slice($items->toArray(), $offset, $perPage);
        //        $itemstoshow = $items->forPage($page, $perPage);
        // Обрезка результатов с учетом постраничного вывода
        //        array_splice($items, $offset, $perPage);

        $urlparts = parse_url(Paginator::resolveCurrentPath()); // for 2 domains fixes
        return new LengthAwarePaginator($itemstoshow, $total, $perPage, $page, [
            'path' => $urlparts['path'],
            'query' => Paginator::resolveQueryString(),
            'pageName' => 'page',
        ]);
    }

    /**
     * @param $sorts
     * @return static
     */
    public static function defaultSort($sorts): static
    {
        static::$defaultSortColumn = $sorts;
        return new static();
    }
}
