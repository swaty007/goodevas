<?php

declare(strict_types=1);

namespace App\Integrations\Data\Enums;

enum UnifiedRefundStatus: string
{
    case PARTIALLY_REFUND = 'partially_refunded';
    case REFUNDED = 'refunded';
    case NOT_REFUNDED = 'not_refunded';
    case ERROR = 'error';
}
