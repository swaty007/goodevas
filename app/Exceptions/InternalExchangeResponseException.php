<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\TelegramSystemLogTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InternalExchangeResponseException extends Exception
{
    use TelegramSystemLogTrait;

    public function render(Request $request): Response|RedirectResponse
    {
        if ($request->inertia() && ! $request->expectsJson()) {
            return redirect()
                ->back()
                ->with(['message_error' => $this->getMessage()]);
        }
        $code = 500;
        if ($this->getPrevious()) {
            $code = $this->getPrevious()->getCode();
        }
        $code = $code ?: 500;
        $message = json_decode($this->getMessage(), true)['message'] ?? $this->getMessage();
        if ($request->expectsJson()) {
            return response([
                'message' => $message,
                'code' => $this->getCode(),
            ], $code);
        }

        return response()->view($this->getCorrectViewName($code), [
            'exception' => $this,
            'message' => $message,
            'code' => $this->getCode(),
        ], $code);
    }

    public function report(): void
    {
        $this->handleException($this);
    }

    private function getCorrectViewName($code): string
    {
        $acceptableCodes = [
            401,
            403,
            404,
            405,
            419,
            429,
            500,
            503,
        ];
        if (in_array($code, $acceptableCodes)) {
            return "errors.$code";
        }

        return 'errors.404';
    }
}
