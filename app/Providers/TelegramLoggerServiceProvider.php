<?php

namespace App\Providers;

use App\Jobs\TelegramMessageJob;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Class TelegramLoggerServiceProvider
 */
class TelegramLoggerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(resource_path('views/vendor/laravel-telegram-logging'), 'laravel-telegram-logging');
        RateLimiter::for('telegram', function (TelegramMessageJob $job) {
            return Limit::perMinute(20)->by($job->token);
        });
    }
}
