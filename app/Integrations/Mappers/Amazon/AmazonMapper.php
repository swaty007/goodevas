<?php

declare(strict_types=1);

namespace App\Integrations\Mappers\Amazon;

use App\Integrations\Data\OrderUnifiedData;
use App\Integrations\Mappers\IntegrationMapperInterface;

class AmazonMapper implements IntegrationMapperInterface
{
    public function map(array $data): OrderUnifiedData {}
}
