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
        'settings',
    ];

    protected $casts = [
        'settings' => 'collection',
    ];

    public function defaultSettings(): array
    {
        return [
            'ranges' => [
                [
                    'max' => 45,
                    'color' => 'danger',
                ],
                [
                    'min' => 45,
                    'max' => 90,
                    'color' => 'warning',
                ],
                [
                    'min' => 90,
                    'max' => 120,
                    'color' => 'success',
                ],
                [
                    'min' => 120,
                    'max' => 180,
                    'color' => 'info',
                ],
                [
                    'min' => 180,
                    'color' => 'purple',
                ],
            ],
        ];
    }

    public function getSettingsAttribute($value): \Illuminate\Support\Collection
    {
        return collect(json_decode($value, true) ?: $this->defaultSettings());
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot(['stock_quantity', 'income_quantity'])->withTimestamps();
    }

    public function incomes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductIncome::class, 'warehouse_id');
    }

    public function futureIncomesDates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->incomes()
            ->where('income_date', '>', now()->subDay()->format('Y-m-d'))
            ->orderBy('income_date', 'asc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
