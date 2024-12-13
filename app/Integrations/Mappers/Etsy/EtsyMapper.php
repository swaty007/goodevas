<?php

declare(strict_types=1);

namespace App\Integrations\Mappers\Etsy;

use App\Integrations\Data\Etsy\OrderEtsyData;
use App\Integrations\Mappers\IntegrationMapperInterface;
use Etsy\Resources\Receipt;

class EtsyMapper implements IntegrationMapperInterface
{
    public function transformOne($data): OrderEtsyData
    {
        $parsedData = json_decode($data->toJson(), true);
        $order = OrderEtsyData::from($parsedData);
        $order->originalObject = $parsedData;

        return $order;
    }

    public function transform(array $data): array
    {
        $result = array_map(function (Receipt $item) {
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
