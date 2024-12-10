<?php

namespace App\Loggers;

use App\Loggers\Handlers\TelegramHandler;
use Monolog\Logger;

/**
 * Class TelegramLogger
 */
class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(
            'telegram',
            [
                new TelegramHandler($config),
            ]
        );
    }
}
