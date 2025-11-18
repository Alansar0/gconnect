<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Voucher;
use App\Models\Reseller;
use App\Models\Router;
use App\Models\VoucherProfile;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\RouterApi\MockMikroTikService;
use App\Services\RouterApi\LiveMikroTikService;

class GetVoucherController extends Controller
{

    protected $routerService;

    public function __construct()
    {
        // Currently using mock; switch to Live when router ready
        $this->routerService = new MockMikroTikService();
    }

    /**
     * Show purchase form.
     */
    public function create()
    {
        $resellers = Reseller::where('status', 'active')->get();
        $profiles = VoucherProfile::all(); // Use real DB model

        return view('getVoucher.buy', compact('resellers', 'profiles'));
    }

    /**
     * Handle a single voucher purchase.
     */

/**
     * Handles the initial selection, saves purchase data to the session,
     * and redirects to the PIN confirmation page (Step 1).
     */
    public function redirectToPinConfirmation(Request $request)
    {
        // 1. Validate only the product details
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'profile_id'  => 'required|exists:voucher_profiles,id',
        ]);

        // Fetch data required for display and transaction
        $profile = VoucherProfile::findOrFail($request->profile_id);

        // 2. Store data in session to be used on the confirmation page
        $request->session()->put('purchase_data', [
            'reseller_id' => $request->reseller_id,
            'profile_id'  => $request->profile_id,
            'amount'      => $profile->price,
            'profile_name'=> $profile->name, // For display on the confirmation page
        ]);

        // 3. Redirect to the PIN confirmation route
        // You MUST define the 'pin.confirmation' route (see section 3 below)
        return redirect()->route('pin.show');
    }

    /**
     * Handles the final transaction after the PIN is verified via AJAX (Step 3).
     * This is where your heavy logic goes. It returns JSON, not a redirect.
     */
    // public function finalStore(Request $request)
    // {
    //     // 1. Validate all required final data, including the PIN
    //     $request->validate([
    //         'reseller_id' => 'required|exists:resellers,id',
    //         'profile_id'  => 'required|exists:voucher_profiles,id',
    //         'pin'         => 'required|digits:4',
    //     ]);

    //     // 2. Authenticate and Verify PIN (Required security check)
    //     $user = Auth::user();
    //     if (!$user || !$user->pin_code || !Hash::check($request->pin, $user->pin_code)) {
    //         // Return JSON error response for the AJAX call
    //         return response()->json(['message' => 'Incorrect transaction PIN.'], 401);
    //     }

    //     // 3. Transaction Logic (Your original code)
    //     $reseller = Reseller::with(['user', 'router'])->findOrFail($request->reseller_id);
    //     $profile  = VoucherProfile::findOrFail($request->profile_id);
    //     $amount   = $profile->price;

    //     if (!$reseller->router) {
    //         return response()->json(['message' => 'Router not found for this reseller.'], 404);
    //     }

    //     $connection = [
    //         'host'     => $reseller->router->host,
    //         'port'     => $reseller->router->port ?? 8728,
    //         'username' => $reseller->router->username,
    //         'password' => encrypt($reseller->router->password),
    //     ];

    //     try {
    //         DB::transaction(function () use ($reseller, $profile, $amount, $connection) {
    //             // Connect to router
    //             if (!$this->routerService->connect($connection)) {
    //                 throw new \Exception('Router connection failed.');
    //             }

    //             // Create voucher on router (mock/live)
    //             $voucherData = $this->routerService->createVoucher([
    //                 'username' => 'V' . strtoupper(Str::random(6)),
    //                 'password' => Str::random(8),
    //                 'profile'  => $profile->mikrotik_profile,
    //             ]);

    //             // Store voucher
    //             Voucher::create([
    //                 'reseller_id' => $reseller->id,
    //                 'profile_id'  => $profile->id,
    //                 'code'        => $voucherData['username'],
    //                 'password'    => $voucherData['password'],
    //                 'status'      => 'active',
    //             ]);

    //             // Handle wallet credit (assuming this credits the reseller)
    //             $wallet = Wallet::firstOrCreate(
    //                 ['user_id' => $reseller->user->id],
    //                 [
    //                     'account_number' => User::generateAccountNumber(),
    //                     'balance' => 0,
    //                 ]
    //             );

    //             $wallet->balance += $amount;
    //             $wallet->save();

    //             // Record transaction
    //             Transaction::create([
    //                 'user_id' => $reseller->user->id,
    //                 'type' => 'credit',
    //                 'amount' => $amount,
    //                 'status' => 'success',
    //                 'reference' => 'VOUCHER-' . strtoupper(Str::random(10)),
    //                 'description' => 'Voucher sale: ' . $profile->name,
    //             ]);
    //         });
    //     } catch (\Throwable $e) {
    //         // Catch any transaction or router errors and return JSON
    //         return response()->json(['message' => 'Purchase failed: ' . $e->getMessage()], 500);
    //     }

    // return response()->json([

    //         'success' => true,
    //         'voucher_id' => $voucher->id,
    //         'code' => $voucher->code,
    //         'password' => $voucher->password,
    //          'receipt_url' => route('getVoucher.receipt', $voucher->id),
    //     ], 200 );

    // }
    public function finalStore(Request $request)
{
    $request->validate([
        'reseller_id' => 'required|exists:resellers,id',
        'profile_id'  => 'required|exists:voucher_profiles,id',
        'pin'         => 'required|digits:4',
    ]);

    $user = Auth::user();
    if (!$user || !$user->pin_code || !Hash::check($request->pin, $user->pin_code)) {
        return response()->json(['message' => 'Incorrect transaction PIN.'], 401);
    }

    $reseller = Reseller::with(['user', 'router'])->findOrFail($request->reseller_id);
    $profile  = VoucherProfile::findOrFail($request->profile_id);
    $amount   = $profile->price;

    if (!$reseller->router) {
        return response()->json(['message' => 'Router not found for this reseller.'], 404);
    }

    $buyerWallet = Wallet::firstOrCreate(
        ['user_id' => $user->id],
        ['account_number' => User::generateAccountNumber(), 'balance' => 0]
    );

    if ($buyerWallet->balance < $amount) {
        return response()->json(['message' => 'Insufficient wallet balance for this purchase.'], 422);
    }

    $connection = [
        'host'     => $reseller->router->host,
        'port'     => $reseller->router->port ?? 8728,
        'username' => $reseller->router->username,
        'password' => encrypt($reseller->router->password),
    ];

    try {
        $voucher = null;
        DB::transaction(function () use ($reseller, $profile, $amount, $connection, $buyerWallet, $user, &$voucher) {
            // Connect to router and create voucher
            if (!$this->routerService->connect($connection)) {
                throw new \Exception('Router connection failed.');
            }

            $voucherData = $this->routerService->createVoucher([
                'username' => 'V' . strtoupper(Str::random(6)),
                'password' => Str::random(8),
                'profile'  => $profile->mikrotik_profile,
            ]);

            // Save voucher (for receipt)
            $voucher = Voucher::create([
                'reseller_id' => $reseller->id,
                'profile_id'  => $profile->id,
                'code'        => $voucherData['username'],
                'password'    => $voucherData['password'],
                'status'      => 'active',
            ]);

            // Debit buyer
            $buyerWallet->balance -= $amount;
            $buyerWallet->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'status' => 'success',
                'reference' => 'VOUCHER-' . strtoupper(Str::random(10)),
                'description' => 'Voucher purchase: ' . $profile->name,
            ]);

            // Credit reseller
            $resellerWallet = Wallet::firstOrCreate(
                ['user_id' => $reseller->user->id],
                ['account_number' => User::generateAccountNumber(), 'balance' => 0]
            );
            $resellerWallet->balance += $amount;
            $resellerWallet->save();

            Transaction::create([
                'user_id' => $reseller->user->id,
                'type' => 'credit',
                'amount' => $amount,
                'status' => 'success',
                'reference' => 'VOUCHER-' . strtoupper(Str::random(10)),
                'description' => 'Voucher sale: ' . $profile->name,
            ]);
        });
    } catch (\Throwable $e) {
        return response()->json(['message' => 'Purchase failed: ' . $e->getMessage()], 500);
    }

    return response()->json([
        'success' => true,
        'voucher_id' => $voucher->id,
        'code' => $voucher->code,
        'password' => $voucher->password,
        'receipt_url' => route('getVoucher.receipt', $voucher->id),
    ], 200);
}


        public function receipt($id)
        {
            $voucher = Voucher::findOrFail($id);

            $transaction = Transaction::where('reference', 'LIKE', "VOUCHER-%")
                ->where('user_id', auth()->id())
                ->where('created_at', $voucher->created_at) // You might want to match voucher ID or other unique identifiers
                ->latest()
                ->first();

            return view('getVoucher.receipt', compact('voucher', 'transaction'));
        }


}
