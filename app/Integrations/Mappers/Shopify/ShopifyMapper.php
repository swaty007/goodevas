<?php

declare(strict_types=1);

namespace App\Integrations\Mappers\Shopify;

use App\Integrations\Data\Shopify\OrderShopifyData;
use App\Integrations\Mappers\IntegrationMapperInterface;

class ShopifyMapper implements IntegrationMapperInterface
{
    public function transformOne($data): OrderShopifyData
    {
        foreach ($data['line_items'] as &$value) {
            $value['order_id'] = $data['id'];
        }
        $order = OrderShopifyData::from($data);
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
