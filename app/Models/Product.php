<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    protected $fillable = [
        'ext_id',
        'ean',
        'additional_data',
    ];

    protected $casts = [
        'additional_data' => 'collection',
    ];

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)->withPivot('stock_quantity')->withTimestamps();
    }

    public function stockSnapshots()
    {
        return $this->hasMany(StockSnapshot::class);
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}