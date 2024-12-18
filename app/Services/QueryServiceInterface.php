<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface QueryServiceInterface
{
    public function getInitQuery(?array $ids = []): Builder;

    public function getIndexQuery(?Builder $initQuery = null): QueryBuilder;

    public function getPluckIndex(): Collection;

    public function getIndexPagination(int|string $perPage = 15, ?Builder $initQuery = null): AbstractPaginator;
}
