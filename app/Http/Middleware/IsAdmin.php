<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check() && User::isAdmin()){
            return $next($request);
        }else{
            return redirect('/login', 301);
        }
    }
}
