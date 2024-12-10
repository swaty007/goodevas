<?php

declare(strict_types=1);

namespace App\Integrations\APIs;

interface IntegrationApiInterface
{
    public function getOrdersList(int $page = 1, int $perPage = 100): array;
}
