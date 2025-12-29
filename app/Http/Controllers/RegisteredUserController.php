<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Reseller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import the Hash Facade for modern hashing
use Illuminate\Validation\Rules\Unique;


class RegisteredUserController extends Controller
{
    public function create()
    {
        $resellers = Reseller::all();
        return view('auth.register', compact('resellers'));
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:11|unique:users,phone_number',
            'password' => 'required|string|min:6|confirmed',
            'reseller_id' => 'required|exists:resellers,id',
        ]);

        // Use Hash::make() for standard password hashing
        $attributes['password'] = Hash::make($attributes['password']);

        $user = User::create($attributes);

        Auth::login($user);

        return redirect()->route('pin.create');
        // return redirect('/dashboard')->with('success', 'Registration successful!');
    }

}
