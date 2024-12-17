<?php

declare(strict_types=1);

namespace App\Integrations\Data\Enums;

enum UnifiedOrderStatus: string
{
    case DONE = 'done';
    case PENDING = 'pending';
    case CANCELED = 'canceled';
    case ERROR = 'error';
}
