<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AppLocked
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Allow PIN routes
        if ($request->routeIs('pin.*')) {
            return $next($request);
        }

        $timeout = 300; // seconds
        $lastActivity = session('last_activity');

        if ($lastActivity && now()->timestamp - $lastActivity > $timeout) {
            session(['app_unlocked' => false]);
        }

        if (!session('app_unlocked', false)) {
            return redirect()->route('pin.authorize');
        }

        return $next($request);
    }
}




