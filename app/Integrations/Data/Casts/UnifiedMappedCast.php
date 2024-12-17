<?php

declare(strict_types=1);

namespace App\Integrations\Data\Casts;

use App\Integrations\Data\Amazon\OrderAmazonData;
use App\Integrations\Data\Enums\UnifiedMappedStatus;
use App\Integrations\Data\Etsy\OrderEtsyData;
use App\Integrations\Data\Shopify\OrderShopifyData;
use App\Models\ApiKey;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class UnifiedMappedCast implements Cast
{
    /**
     * @param  ?string  $value  — входящее значение статуса
     * @param  CreationContext  $context  — доп. данные о том, откуда заказ?
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): UnifiedMappedStatus
    {
        $type = $properties['type'];

        return match ($type) {
            ApiKey::TYPE_AMAZON => OrderAmazonData::getStatusMap()[$value],
            ApiKey::TYPE_SHOPIFY => OrderShopifyData::getStatusMap()[$value],
            ApiKey::TYPE_ETSY => OrderEtsyData::getStatusMap()[$value],
            default => UnifiedMappedStatus::ERROR_PARSE,
        };
    }
}
