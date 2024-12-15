<?php

declare(strict_types=1);

namespace App\Integrations\APIs;

use Illuminate\Support\Carbon;

interface IntegrationApiInterface
{
    public function getOrdersList(Carbon $createdMin, ?Carbon $createdMax = null, int $perPage = 100, array $options = []): array;
}
