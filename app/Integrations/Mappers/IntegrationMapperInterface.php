<?php

declare(strict_types=1);

namespace App\Integrations\Mappers;

use App\Data\OrderUnifiedData;

interface IntegrationMapperInterface
{
    public function map(array $data): OrderUnifiedData;
}
