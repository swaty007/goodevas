<?php

namespace App\Providers;

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
    }
}
