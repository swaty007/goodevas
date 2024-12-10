<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;

class ProcessOrdersJob
{
    public function handle()
    {
        $files = [
            'orders_ApiOneAdapter.json',
            'orders_ApiTwoAdapter.json',
        ];

        $allOrders = [];
        foreach ($files as $file) {
            $data = json_decode(Storage::get($file), true);
            foreach ($data as $order) {
                // Преобразуем в DTO
                $mapper = app(OrderDtoMapperInterface::class);
                $allOrders[] = $mapper->map($order);
            }
        }

        // Дальнейшая обработка заказов
        dd($allOrders);
    }
}
