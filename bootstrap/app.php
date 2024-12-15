<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\TelegramHandlerCommand::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // $schedule->command('telescope:prune')->cron('0 0 * * *');
        $schedule->command('activitylog:clean')->cron('10 0 * * *');
        $schedule->command('model:prune')->cron('20 0 * * *');
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
        $schedule->command('horizon:snapshot')->everyFifteenMinutes();
        $schedule->command('command:orders-parser --days=1')->everyThirtyMinutes();
        $schedule->command('queue:prune-failed --hours=168')->everyFifteenMinutes();
        $schedule->command('queue:prune-batches --hours=48 --unfinished=72')->everyFifteenMinutes();
        $schedule->command(\App\Console\Commands\TelegramHandlerCommand::class)
            ->everyThirtySeconds()
            ->withoutOverlapping(1);
        $schedule->command(\App\Console\Commands\ProductsParserCommand::class)
            ->everyFiveMinutes()
            ->withoutOverlapping(5);
        $schedule->command(\App\Console\Commands\ProductsSnapshotCommand::class)
            ->hourly()
            ->withoutOverlapping(5);
        $schedule->command(\App\Console\Commands\EtsyUpdateRefreshTokens::class)
            ->everyThirtyMinutes();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'etsy/auth-callback',
            'admin/webhooks/*',
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withSingletons([
        Illuminate\Contracts\Debug\ExceptionHandler::class => App\Exceptions\Handler::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
