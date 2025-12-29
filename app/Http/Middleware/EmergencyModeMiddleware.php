<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmergencyModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
  public function handle(Request $request, Closure $next)
{
    if (! cache()->get('emergency_mode', false)) {
        return $next($request);
    }

    // ✅ Allow super admin ALWAYS
    if (auth()->check() && auth()->user()->is_super_admin) {
        return $next($request);
    }

    // ❌ Block everyone else
    return response()->view('components.errors.emergency', [], 503);
}

}
