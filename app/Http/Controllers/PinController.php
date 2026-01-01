<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laragear\WebAuthn\WebAuthn;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Transaction;

class PinController extends Controller
{
    // show create pin form (first entry or confirmation)
    public function create()
    {
        return view('auth.create-pin');
    }

    /**
     * Store PIN. Two-step confirmation flow:
     * - If session('pin_temp') is empty => store pin temp in session and redirect back to confirm
     * - If session('pin_temp') exists => verify confirmation and persist hashed pin to DB
     */
    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $pin = $request->input('pin');

        // if no temp pin yet, save to session and ask to confirm
        if (! $request->session()->has('pin_temp')) {
            $request->session()->put('pin_temp', $pin);
            return back()->with('status', 'Please re-enter your 4-digit PIN to confirm.');
        }

        // confirmation step
        $temp = $request->session()->get('pin_temp');

        if ($temp !== $pin) {
            // mismatch: clear temp and ask to restart
            $request->session()->forget('pin_temp');
            return back()->withErrors(['pin' => 'PINs do not match. Start again.']);
        }

        // save hashed PIN to user and clear temp
        $user = Auth::user();
        $user->pin_code = Hash::make($pin);
        $user->save();

        $request->session()->forget('pin_temp');

        // mark app as unlocked for the current session
        $request->session()->put('app_unlocked', true);

        return redirect('/dashboard')->with('success', 'PIN created successfully.');
    }

    // show lock screen to enter PIN for authorization (session expired)
    public function showLockScreen()
    {
        return view('auth.lock-screen'); // blade provided below
    }

    // check authorize pin
    public function showLockScreenCheck(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);

        $user = Auth::user();

        if (! $user || ! $user->pin_code) {
            return back()->withErrors(['pin' => 'PIN not set.']);
        }

        if (! Hash::check($request->pin, $user->pin_code)) {
            return back()->withErrors(['pin' => 'Incorrect PIN.']);
        }

        // authorize and redirect to intended
       session([
        'app_unlocked' => true,
        'last_activity' => now()->timestamp,
    ]);


        return redirect()->intended('/dashboard');
    }
       public function showPinPage()
    {
        $voucher = Voucher::all();

        return view('getVoucher.transaction-pin', compact('voucher'));
    }

    // verify pin for a sensitive transaction (AJAX)

        public function verifyTransactionPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);

        $user = Auth::user();

        if (! $user || ! $user->pin_code) {
            return response()->json(['verified' => false, 'message' => 'PIN not set.'], 422);
        }

        if (! Hash::check($request->pin, $user->pin_code)) {
            return response()->json(['verified' => false, 'message' => 'Incorrect PIN.'], 401);
        }

        session(['pin_verified' => true]);

        return response()->json(['verified' => true], 200);
    }
        public function showBiometricRegister()
    {
        return view('profile.biometric-register');
    }


        public function toggleBiometric(Request $request)
    {
        $user = auth()->user();

        if (! $user->has_biometric) {
            return redirect()->route('biometric.register.view');
        }

        $user->update([
            'has_biometric' => false,
        ]);

        return back()->with('success', 'Biometric disabled');
    }

}
