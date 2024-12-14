<?php

declare(strict_types=1);

namespace App\Integrations\Data;

interface OrderDataInterface
{
    public static function convertToUnified(): OrderUnifiedData;

    public static function getStatusMap(): array;
}
