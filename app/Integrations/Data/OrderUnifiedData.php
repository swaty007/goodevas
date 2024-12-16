<?php

namespace App\Integrations\Data;

use App\Integrations\Data\Casts\UnifiedStatusCast;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class OrderUnifiedData extends Data
{
    public function __construct(
        public string $type,
        public string $order_id,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?DateTime $order_date,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?DateTime $update_date,
        #[WithCast(UnifiedStatusCast::class)]
        public Enums\UnifiedOrderStatus $order_status,
        public ?string $fulfillment,
        public ?string $sales_channel,
        public string $total_amount,
        public string $total_currency,
        public ?string $payment_method,

        // Адрес и данные покупателя
        public ?string $buyer_name,
        public ?string $address_line_1,
        public ?string $address_line_2,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country_code,

        // Показатели обработки и отгрузки (актуально для Etsy)
        //        public ?int $min_processing_days = null,
        //        public ?int $max_processing_days = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?DateTime $expected_ship_date = null,

        public bool $is_shipped = false,
        /** @var ItemUnifiedData[] */
        public array $items = [],
        public array $refunds = [],
        public mixed $original_object = null,
    ) {}
}
