<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'order_id',
        'item_id',
        'api_order_id',
        'quantity',
        'title',
        'sku',
    ];

    protected $casts = [
        'warehouse_stock' => 'collection',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderByApiKey(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'api_order_id', 'order_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
