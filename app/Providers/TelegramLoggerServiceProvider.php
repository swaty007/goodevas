<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class TelegramLoggerServiceProvider
 * @package Logger
 */
class TelegramLoggerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(resource_path('views/vendor/laravel-telegram-logging'), 'laravel-telegram-logging');
    }
}
