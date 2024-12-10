<?php

declare(strict_types=1);

namespace App\Integrations\Mappers;

use App\Data\OrderData;

interface IntegrationMapperInterface {
    public function map(array $data): OrderData;
}
