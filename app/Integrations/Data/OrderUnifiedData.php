<?php

namespace App\Integrations\Data;

use Spatie\LaravelData\Data;

class OrderUnifiedData extends Data
{
    public function __construct(
        public string $order_id,
        public ?string $order_date,
        public ?string $update_date,
        public string $order_status,
        public ?string $fulfillment,
        public ?string $sales_channel,
        public array $total,
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
        public ?int $min_processing_days = null,
        public ?int $max_processing_days = null,
        public ?string $expected_ship_date = null,

        public bool $is_shipped = false,
        /** @var TransactionUnifiedData[] */
        public array $transactions = [],
        public array $refunds = [],
        public mixed $originalObject = null,
    ) {}
}
