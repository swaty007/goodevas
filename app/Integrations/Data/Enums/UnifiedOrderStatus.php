<?php

declare(strict_types=1);

namespace App\Integrations\Data\Enums;

enum UnifiedOrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case PARTIALLY_REFUND = 'partially_refunded';
    case REFUNDED = 'refunded';
    case CANCELED = 'canceled';
    case PARTIALLY_SHIPPED = 'partially_shipped';
    case SHIPPED = 'shipped';
    case ERROR_PARSE = 'error_parse';
}
