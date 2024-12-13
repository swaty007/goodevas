<?php

declare(strict_types=1);

namespace App\Integrations\Data;

interface ItemDataInterface
{
    public static function convertToUnified(): ItemUnifiedData;
}
