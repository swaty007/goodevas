<?php

declare(strict_types=1);

namespace App\Integrations\Mappers\Amazon;

use App\Integrations\Data\Amazon\OrderAmazonData;
use App\Integrations\Mappers\IntegrationMapperInterface;

class AmazonMapper implements IntegrationMapperInterface
{
    public function transformOne($data): OrderAmazonData
    {
        $order = OrderAmazonData::from($data);
        $order->originalObject = $data;

        return $order;
    }

    public function transform(array $data): array
    {
        $result = array_map(function ($item) {
            return $this->transformOne($item);
        }, $data);

        return $result;
    }

    public function transformToUnified(array $data): array
    {
        $result = $this->transform($data);
        $result = array_map(fn ($i) => $i::convertToUnified($i), $result);

        return $result;
    }
}
