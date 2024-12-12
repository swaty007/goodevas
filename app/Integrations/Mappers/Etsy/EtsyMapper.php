<?php
declare(strict_types=1);

namespace App\Integrations\Mappers\Etsy;

use App\Data\OrderUnifiedData;
use App\Integrations\Data\Etsy\OrderEtsyData;
use App\Integrations\Mappers\IntegrationMapperInterface;

class EtsyMapper implements IntegrationMapperInterface
{
    public function map(array $data): OrderUnifiedData
    {
        $result = array_map(function ($item) {
            dump($item);
            dd(OrderEtsyData::from($item->toJson()));
            return OrderEtsyData::from($item->toJson());
        }, $data);
        dd($result);
    }
}
