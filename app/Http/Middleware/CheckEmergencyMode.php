<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EmergencyLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmergencyMode
        {
            // public function handle(Request $request, Closure $next)
            // {
            //     if (config('app.emergency_mode') && (!auth()->check() || !auth()->user()->isAdmin())) {
            //         return response()->view('errors.emergency'); // Or abort(503)
            //     }

            //     return $next($request);
            // }
            public function handle(Request $request, Closure $next)
{
    if (
        cache('emergency_mode', false) &&
        (!auth()->check() || !auth()->user()->isAdmin())
    ) {
        return response()->view('components.errors.emergency', [], 503);
    }

    return $next($request);
}

        }
