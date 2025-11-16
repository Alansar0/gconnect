<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AppLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // If user is authenticated but app not unlocked, redirect to pin authorize
        if (Auth::check() && ! $request->session()->get('app_unlocked', false)) {

            // allow the pin creation page and authorize endpoints and logout routes to avoid loop
            $allow = [
                route('pin.create'),
                route('pin.store'),
                route('pin.authorize'),
                route('pin.authorize.check'),
                route('logout'), // if you have named route
            ];

            if (! in_array($request->url(), $allow)) {
                return redirect()->route('pin.authorize');
            }
        }
        
        return $next($request);
    }
}



