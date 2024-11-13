<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'country_id',
        'virtual',
        'ysell_name',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['stock_quantity', 'income_quantity'])->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
