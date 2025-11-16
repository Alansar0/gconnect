<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle the incoming authentication request.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Determine if the login field is an email or phone number
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';


        $authCredentials = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
        ];



        // 4. Attempt authentication
        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // 5. If attempt failed, throw validation error back to the form
        throw ValidationException::withMessages([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    // public function destroy(Request $request)
    // {
    //     Auth::guard('web')->logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect('/login');
    // }

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

    return redirect('/login');
}

}

