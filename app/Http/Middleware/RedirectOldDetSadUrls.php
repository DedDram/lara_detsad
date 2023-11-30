<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectOldDetSadUrls
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Проверяем, начинается ли URL с "/"
        if (strpos($path, 'detskie-sady/') === 0) {
            // Если да, то выполните редирект на новый URL
            $newPath = substr($path, strlen('detskie-sady/'));
            return redirect('/' . $newPath, 301);
        }

        return $next($request);
    }
}
