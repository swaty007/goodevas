<?php

namespace App\Loggers;

use App\Loggers\Handlers\TelegramHandler;
use Monolog\Logger;

/**
 * Class TelegramLogger
 * @package App\Logging
 */
class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
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
