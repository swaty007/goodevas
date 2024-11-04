<?php

namespace Brackets\CraftablePro\Http\Middleware;

use App\Settings\GeneralSettings;
use Closure;

class SetLocale
{
    /**
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && ! empty($request->user()->locale)) {
            app()->setLocale($request->user()->locale);
        } else {
            app()->setLocale(app(GeneralSettings::class)->default_locale);
        }

        return $next($request);
    }
}
