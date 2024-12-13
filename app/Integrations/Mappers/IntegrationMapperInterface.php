<?php

declare(strict_types=1);

namespace App\Integrations\Mappers;

use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;

/**
 * @template T
 */
interface IntegrationMapperInterface
{
    public function transformOne(array|object $data): OrderDataInterface;

    /**
     * @param  T[]  $data
     * @return OrderDataInterface[]
     */
    public function transform(array $data): array;

    /**
     * @param  T[]  $data
     * @return OrderUnifiedData[]
     */
    public function transformToUnified(array $data): array;
}
