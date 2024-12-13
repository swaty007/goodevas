<?php

declare(strict_types=1);

namespace App\Integrations\Data;

interface TransactionDataInterface
{
    public static function convertToUnified(): TransactionUnifiedData;
}
