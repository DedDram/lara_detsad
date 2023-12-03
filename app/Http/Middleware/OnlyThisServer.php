<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class OnlyThisServer
{
    public function handle(Request $request, Closure $next)
    {
        if (env('APP_HOST') == $request->ip()) {
            return $next($request);
        } else {
            abort(403);
        }
    }
}

