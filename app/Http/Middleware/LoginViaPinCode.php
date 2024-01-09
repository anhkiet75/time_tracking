<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginViaPinCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('login')) {
            // Auth::logout();
            // $request->session()->invalidate();
            // $request->session()->regenerateToken();
            return $next($request);
        }
        if (Auth::check() && !Auth::user()->use_pin_code) {
            return $next($request);
        }
        return abort(403);
    }
}
