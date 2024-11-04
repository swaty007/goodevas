<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StockSnapshot extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'snapshot_date',
        'warehouse_stock',
    ];

    protected $casts = [
        'warehouse_stock' => 'collection',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateStockChanges($productId, $startDate, $endDate)
    {
        $snapshots = StockSnapshot::where('product_id', $productId)
            ->whereBetween('snapshot_date', [$startDate, $endDate])
            ->orderBy('snapshot_date')
            ->get();

        $totalConsumption = 0;
        $totalRestock = 0;

        for ($i = 1; $i < $snapshots->count(); $i++) {
            $previousStock = array_sum($snapshots[$i - 1]->warehouse_stock);
            $currentStock = array_sum($snapshots[$i]->warehouse_stock);
            $difference = $currentStock - $previousStock;

            if ($difference < 0) {
                $totalConsumption += abs($difference);
            } elseif ($difference > 0) {
                $totalRestock += $difference;
            }
        }

        return [
            'total_consumption' => $totalConsumption,
            'total_restock' => $totalRestock,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
