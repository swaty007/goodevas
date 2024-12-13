<?php

declare(strict_types=1);

namespace App\Integrations\Mappers\Shopify;

use App\Integrations\Data\OrderUnifiedData;
use App\Integrations\Mappers\IntegrationMapperInterface;

class ShopifyMapper implements IntegrationMapperInterface
{
    public function map(array $data): OrderUnifiedData {}
}
