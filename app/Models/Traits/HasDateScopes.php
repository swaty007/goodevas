<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Helpers\TimeParser;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait HasDateScopes
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function scopeStartDate(Builder $query, $date = null): Builder
    {
        try {
            if (empty($date)) {
                $date = Paginator::resolveQueryString()['filter']['start_date'];
            }
            $date = TimeParser::parseStringToDate($date);

            return $query->where("{$query->getModel()->getTable()}.order_date", '>=', $date);
        } catch (Exception $e) {
            return $query;
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function scopeEndDate(Builder $query, $date = null): Builder
    {
        try {
            if (empty($date)) {
                $date = Paginator::resolveQueryString()['filter']['end_date'];
            }
            $date = TimeParser::parseStringToDate($date);

            return $query->where("{$query->getModel()->getTable()}.order_date", '<=', $date);
        } catch (Exception $e) {
            return $query;
        }
    }
}
