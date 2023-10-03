<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectOldDetSadUrls
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Проверяем, начинается ли URL с "/"
        if (strpos($path, 'detskie-sady/') === 0) {
            // Если да, то выполните редирект на новый URL
            $newPath = substr($path, strlen('detskie-sady/'));
            return redirect('/' . $newPath);
        }

        return $next($request);
    }
}
