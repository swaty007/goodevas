<?php

namespace App\Exceptions;

use App\Traits\TelegramSystemLogTrait;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Lottery;
use Throwable;

class Handler extends ExceptionHandler
{
    use TelegramSystemLogTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Throttle the given exception.
     *
     * @return Lottery|Limit|null
     */
    public function throttle(Throwable $e)
    {
        if ($e instanceof InternalExchangeResponseException) {
            return Limit::perMinute(30);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        $prepareException = $this->prepareException($e);

        if (
            ! $this->shouldReport($prepareException)
        ) {
            return parent::render($request, $e);
        }
        $this->handleException($e);

        //        if ($request->inertia() && !$request->expectsJson()) {
        //            return redirect()
        //                ->back()
        //                ->with(['message_error' => $e->getMessage()]);
        //        }
        return parent::render($request, $e);
    }
}
