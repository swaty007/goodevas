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
        $shipments = [];
        foreach ($data->shipments as $shipment) {
            $shipments[] = $shipment->toArray();
        }
        $data->shipments = $shipments;

        $parsedData = json_decode($data->toJson(), true);
        $transactions = [];
        foreach ($parsedData['transactions'] as $transaction) {
            if (str_contains($transaction['sku'], '+')) {
                $n = 0;
                foreach (explode('+', $transaction['sku']) as $sku) {
                    $transaction['sku'] = $sku;
                    if ($n > 0) {
                        $transaction['transaction_id'] .= '_'.$sku;
                    }
                    $transactions[] = $transaction;
                    $n++;
                }
            } else {
                $transactions[] = $transaction;
            }
        }
        $parsedData['transactions'] = $transactions;

        $order = OrderEtsyData::from($parsedData);
        $order->original_object = json_decode($data->toJson(), true);

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
