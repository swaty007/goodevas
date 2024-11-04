<?php

namespace Brackets\CraftablePro\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackLastActive
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('craftable-pro.track_user_last_active_time') || ! $request->user()) {
            return $next($request);
        }

        if (
            ! $request->user()->last_active_at ||
            ($request->user()->last_active_at->addSeconds(60)->isPast() &&
                ! Cache::has('user-online-'.$request->user()->getTable().'_'.$request->user()->id))
        ) {
            Cache::put('user-online-'.$request->user()->getTable().'_'.$request->user()->id, true, 60);
            $request->user()->last_active_at = now();
            $request->user()->saveQuietly();
        }

        return $next($request);
    }
}
