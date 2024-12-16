<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\InternalExchangeResponseException;
use App\Jobs\TelegramMessageJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

trait TelegramSystemLogTrait
{
    private static string $telegram_icon_sos = 'F09F8698';

    private static string $telegram_icon_warning = 'f09f9ab8';

    private string $slackWebhook = 'https://hooks.slack.com/services/';

    //Find bot info
    //https://api.telegram.org/bot<token>/getUpdates

    public function handleException(Throwable $e): void
    {
        if (App::environment('production')) {
            try {
                $text = 'Server error | on '.$this->getAppName();
                $text = hex2bin(self::$telegram_icon_sos).'<b>'.$text."</b>\n";
                $text .= 'Class: '.get_class($e)."\n";
                $text .= 'File: '.$e->getFile()."\n";
                $text .= 'Line: '.$e->getLine()."\n";
                $text .= 'Code: '.$e->getCode()."\n";
                $text .= 'User/Admin ID: '.Auth::id()."\n";
                $text .= 'Error: '.$e->getMessage()."\n";
                $trace = $e->getTrace();
                if (! $e instanceof InternalExchangeResponseException) {
                    $text .= '<blockquote expandable><pre><code class="language-php">';
                    for ($i = 0; $i < min(10, count($trace)); $i++) {
                        $text .= 'Trace '.($i + 1).': File: '.$trace[$i]['file'].' Line: '.$trace[$i]['line']."\n";
                    }
                    $text .= '</code></pre></blockquote>';
                }
                $this->sendImportantMessage($text);
            } catch (Throwable $e) {
                $this->sendImportantMessage('Server error | on '.env('APP_NAME')."\n".'Error: '.$e->getMessage());
            }
        }
    }

    public function errorMessageGroup($error = ''): void
    {
        if (App::environment('production')) {
            try {
                $text = 'Server error | on '.$this->getAppName();
                $text = hex2bin(self::$telegram_icon_sos).'<b>'.$text."</b>\n";
                $text .= 'Error: '.$error;

                $this->sendImportantMessage($text);
            } catch (Throwable $e) {

            }
        }
    }

    public function infoMessageGroup($error = ''): void
    {
        if (App::environment('production')) {
            try {
                $text = 'Server info | on '.$this->getAppName();
                $text = hex2bin(self::$telegram_icon_warning).'<b>'.$text."</b>\n";
                $text .= 'Info: '.$error."\n";
                $this->sendMinorMessage($text);

            } catch (Throwable $e) {

            }
        }
    }

    private function sendImportantMessage($text): void
    {
        $this->sendTelegramChatMessage($text, config('telegram-logger.chat_id'));
    }

    private function sendMinorMessage($text): void
    {
        $this->sendTelegramChatMessage($text, config('telegram-logger.chat_id'));
    }

    private function sendTelegramChatMessage($text, $chat_id): void
    {
        TelegramMessageJob::dispatch(config('telegram-logger.token'), $chat_id, $text);
    }

    public function sendMessageSlack($text): void
    {
        $ch = curl_init($this->slackWebhook);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'text' => strip_tags($text),
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
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
