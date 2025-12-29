<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }


public function store(Request $request)
{
    $credentials = $request->validate([
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

    // Find the user by email or phone
    $user = \App\Models\User::where($loginField, $credentials['login'])->first();

    if (!$user) {
        // User not found
        throw ValidationException::withMessages([
            'login' => "No account found with this {$loginField}.",
        ]);
    }

    if ($user->is_blocked) {
        throw ValidationException::withMessages([
            'login' => 'Your account has been blocked. Please contact support.',
        ]);
    }

    // Check password manually
    if (!Hash::check($credentials['password'], $user->password)) {
        throw ValidationException::withMessages([
            'password' => 'The password is incorrect.',
        ]);
    }

    // Login
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    return redirect()->intended('/dashboard');
}

    public function destroy(Request $request)
{
    $user = Auth::user();
    if ($user) {
        $user->pin_code = null;
        $user->has_biometric = false;
        $user->save();
    }

    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    
    // IMPORTANT
    $request->session()->forget(['app_unlocked', 'pin_verified']);

    return redirect('/login');
}

}

