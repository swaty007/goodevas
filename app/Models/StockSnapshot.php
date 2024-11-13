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
        'warehouse_income',
    ];

    protected $casts = [
        'warehouse_stock' => 'collection',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
