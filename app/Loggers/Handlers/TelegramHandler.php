<?php

namespace App\Loggers\Handlers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Throwable;

/**
 * Class TelegramHandler
 */
class TelegramHandler extends AbstractProcessingHandler
{
    private array $config;

    private string $botToken;

    private int $chatId;

    private string $appName;

    private string $appEnv;

    /**
     * TelegramHandler constructor.
     */
    public function __construct(array $config)
    {
        $level = Logger::toMonologLevel($config['level']);

        parent::__construct($level, true);

        // define variables for making Telegram request
        $this->config = $config;
        $this->botToken = $this->getConfigValue('token');
        $this->chatId = $this->getConfigValue('chat_id');

        // define variables for text message
        $this->appName = $this->getAppName();
        $this->appEnv = config('app.env');
    }

    /**
     * @param  array  $record
     */
    public function write($record): void
    {
        if (! $this->botToken || ! $this->chatId) {
            throw new \InvalidArgumentException('Bot token or chat id is not defined for Telegram logger');
        }

        // trying to make request and send notification
        try {
            $textChunks = str_split($this->formatText($record), 4096);

            foreach ($textChunks as $textChunk) {
                if (count($textChunks) > 1) {
                    $textChunk = $this->updateTextForTelegram($textChunk);
                }
                $this->sendMessage($textChunk);
            }
        } catch (Exception $exception) {
            Log::channel('single')->error($exception->getMessage());
        }
    }

    private function updateTextForTelegram($text): string
    {
        $allowed_tags = '<b><strong><i><em><u><ins><s><strike><del><a><code><pre><tg-spoiler><blockquote>';
        $text = strip_tags($text, $allowed_tags);
        $text = str_replace([' < ', ' > ', '&'], '', $text);
        $text = substr($text, 0, 4095);

        return $text;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        // $format = "%message% %context% %extra%\n";
        $format = "%message% %extra%\n";

        return new LineFormatter($format, null, true, true);
    }

    private function formatText($record): string
    {
        if ($template = config('telegram-logger.template')) {
            if ($record instanceof LogRecord) {
                return view(
                    $template,
                    array_merge($record->toArray(), [
                        'appName' => $this->appName,
                        'appEnv' => $this->appEnv,
                        'formatted' => $record->formatted,
                        'icon' => match ($record->level) {
                            Level::Debug => '😊',
                            Level::Info => 'ℹ️',
                            Level::Notice => '👀',
                            Level::Warning => '⚠️',
                            Level::Error => '🆘',
                            Level::Critical => '🔥',
                            Level::Alert => '🚨',
                            Level::Emergency => '💀',
                            default => '🆘',
                        },
                    ])
                )->render();
            }

            return view(
                $template,
                array_merge($record, [
                    'appName' => $this->appName,
                    'appEnv' => $this->appEnv,
                    'icon' => match ($record?->level) {
                        Level::Debug => '😊',
                        Level::Info => 'ℹ️',
                        Level::Notice => '👀',
                        Level::Warning => '⚠️',
                        Level::Error => '🆘',
                        Level::Critical => '🔥',
                        Level::Alert => '🚨',
                        Level::Emergency => '💀',
                        default => '🆘',
                    },
                ])
            )->render();
        }

        return sprintf("<b>%s</b> (%s)\n%s", $this->appName, $record['level_name'], $record['formatted']);
    }

    private function sendMessage(string $text): void
    {
        $host = $this->getConfigValue('api_host');
        $proxy = $this->getConfigValue('proxy');
        $url = $host.'/bot'.$this->botToken.'/sendMessage';
        $options = config('telegram-logger.options', []);

        $response = Http::withOptions([
            'proxy' => $proxy,
        ])->asForm()->post($url, array_merge([
            'text' => $text,
            'chat_id' => $this->chatId,
            'parse_mode' => 'html',
        ], $options));

        if (! $response->successful()) {
            Log::channel('single')->error('Failed to send Telegram message', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * @param  string  $key
     * @param  string  $defaultConfigKey
     */
    private function getConfigValue($key, $defaultConfigKey = null): ?string
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return config($defaultConfigKey ?: "telegram-logger.$key");
    }

    private function getAppName(): string
    {
        try {
            return config('app.name', ___('global', 'Platform', ['site_name' => config('app.name', 'Platform')])).' | '.Request::getHost();
        } catch (Throwable $e) {
            return env('APP_NAME').' | '.Request::getHost();
        }
    }
}
