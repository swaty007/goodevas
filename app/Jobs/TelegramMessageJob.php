<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramMessageJob implements ShouldQueue // , ShouldBeEncrypted
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 10;

    public int $backoff = 5;

    public function __construct(public string $token, public string $chatId, public string $text)
    {
        $this->token = Crypt::encrypt($token);
    }

    public function middleware(): array
    {
        return [new RateLimited('telegram')];
    }

    public function handle(): void
    {
        try {
            Telegram::setAccessToken(Crypt::decrypt($this->token));
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => $this->updateTextForTelegram($this->text),
                'parse_mode' => 'HTML',
            ]);

            //        $host = $this->getConfigValue('api_host');
            //        $proxy = $this->getConfigValue('proxy');
            //        $url = $host . '/bot' . $this->botToken . '/sendMessage';
            //        $options = config('telegram-logger.options', []);
            //        $response = Http::withOptions([
            //            'proxy' => $proxy,
            //        ])->asForm()->post($url, array_merge([
            //            'text'       => $text,
            //            'chat_id'    => $this->chatId,
            //            'parse_mode' => 'html',
            //        ], $options));
            //        if (!$response->successful()) {
            //            Log::channel('single')->error('Failed to send Telegram message', [
            //                'status' => $response->status(),
            //                'body' => $response->body(),
            //            ]);
            //        }
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
}
