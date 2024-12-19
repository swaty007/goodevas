<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\LogBatch;
use Throwable;

class OrderManager
{
    public function store(array $orderData): Order
    {
        $order = Order::create($orderData);

        if (! empty($orderData['orderItems_ids'])) {
            // $order->items()->createMany($orderData['orderItems_ids']);
        }

        return $order;
    }

    public function update(array $orderData, Order $order, bool $bulkAction = false): Order
    {
        $orderData['manual_changed'] = true;
        $order->update($orderData);

        if (! empty($orderData['orderItems_ids'])) {
            // $order->items()->createMany($orderData['orderItems_ids']);
        }

        return $order;
    }

    public function bulkUpdate(array $ordersIds, array $orderData): string
    {
        if (empty($orderData['admins_ids'])) {
            unset($orderData['admins_ids']);
        }
        LogBatch::startBatch();
        $idsChunks = array_chunk($ordersIds, 10000);
        foreach ($idsChunks as $idsChunk) {
            Order::whereIn('id', $idsChunk)->chunk(500, function ($bulkChunk) use ($orderData) {
                try {
                    DB::beginTransaction();
                    $bulkChunk->each(function ($order) use ($orderData) {
                        $this->update($orderData, $order, true);
                    });
                    DB::commit();
                } catch (Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            });
        }
        $batchUuid = LogBatch::getUuid();
        LogBatch::endBatch();

        return $batchUuid;
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }

    public function restore(Order $order): void
    {
        $order->restore();
    }
}
