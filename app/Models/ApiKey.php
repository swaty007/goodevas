<?php

/** Auto-generated by Craftable PRO */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ApiKey extends Model
{
    use LogsActivity;

    protected $table = 'api_keys';

    protected $fillable = [
        'name',
        'type',
        'key',
        'additional_data',
    ];

    protected $casts = [
        'key' => 'collection',
        'additional_data' => 'collection',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}