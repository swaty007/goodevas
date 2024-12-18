<?php

declare(strict_types=1);

namespace App\Integrations\Data;

use App\Integrations\Data\Enums\UnifiedFulfilmentStatus;
use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\Enums\UnifiedRefundStatus;

interface OrderDataInterface
{
    public static function convertToUnified(): OrderUnifiedData;

    public static function getStatusMap(): array;

    public static function resolveOrderStatus(): UnifiedOrderStatus;

    public static function resolveFulfillmentStatus(): UnifiedFulfilmentStatus;

    public static function resolveRefundStatus(): UnifiedRefundStatus;
}
