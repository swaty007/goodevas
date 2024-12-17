<?php

declare(strict_types=1);

namespace App\Integrations\Data\Enums;

enum UnifiedFulfilmentStatus: string
{
    case SHIPPED = 'shipped';
    case PARTIALLY_SHIPPED = 'partially_shipped';
    case NOT_SHIPPED = 'not_shipped';
    case ERROR = 'error';
}
