<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        return Auth::check() && Auth::user()->is_super_admin === true;
    }
}
