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

    /**
     * Handle the incoming authentication request.
     */
//     public function store(Request $request)
// {
//     // 1. Validate the incoming request data
//     $credentials = $request->validate([
//         'login' => ['required', 'string'],
//         'password' => ['required', 'string'],
//     ]);

//     // 2. Determine if the login field is an email or phone number
//     $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

//     // 3. Retrieve the user by email or phone
//     $user = \App\Models\User::where($loginField, $credentials['login'])->first();

//     // 4. Deny login if user is blocked
//     if ($user && $user->is_blocked) {
//         throw ValidationException::withMessages([
//             'login' => 'Your account has been blocked. Please contact support.',
//         ]);
//     }

//     // 5. Attempt authentication
//     $authCredentials = [
//         $loginField => $credentials['login'],
//         'password' => $credentials['password'],
//     ];

//     if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
//         $request->session()->regenerate();
//         return redirect()->intended('/dashboard');
//     }

//     // 6. If attempt failed, throw validation error
//     throw ValidationException::withMessages([
//         'login' => 'The provided credentials do not match our records.',
//     ]);
// }

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

    return redirect('/login');
}

}

