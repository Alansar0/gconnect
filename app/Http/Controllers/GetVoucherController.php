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
use App\Models\RouterSetting;
use App\Models\VoucherQueue;
use Carbon\Carbon;
use App\Models\Waitlist;
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

         private function getActiveExpiryTimesForReseller(int $resellerId): array
    {
        return VoucherQueue::where('reseller_id', $resellerId)
            ->where('expiry_time', '>', now())
            ->orderBy('expiry_time', 'asc')
            ->pluck('expiry_time')
            ->map(fn($t) => Carbon::parse($t))
            ->toArray();
    }

    // compute expected available time for a new waiter given profile duration
    private function calculateExpectedAvailableAt(int $resellerId, ?VoucherProfile $profile): Carbon
    {
        $expiryTimes = $this->getActiveExpiryTimesForReseller($resellerId);
        $waitingCount = Waitlist::where('reseller_id', $resellerId)->where('status','waiting')->count();

        if ($waitingCount < count($expiryTimes)) {
            // assign the next free expiry
            return $expiryTimes[$waitingCount];
        }

        // extend beyond last expiry
        $last = end($expiryTimes) ?: now();
        $minutes = $profile ? (int)$profile->time_minutes : 60;
        $extraIndex = $waitingCount - count($expiryTimes) + 1;

        return Carbon::parse($last)->addMinutes($minutes * $extraIndex);
    }

    // get total active connections (wan1 + wan2)
    private function totalActiveConnections(RouterSetting $settings): int
    {
        return (int)$settings->wan1_current_count + (int)$settings->wan2_current_count;
    }

    // choose a WAN deterministically: always fill wan1 first, then wan2
    private function chooseWan(RouterSetting $settings): string
    {
        if ($settings->wan1_current_count < $settings->wan1_limit) {
            return 'ether1';
        }
        if ($settings->wan2_current_count < $settings->wan2_limit) {
            return 'ether2';
        }
        // both full (shouldn't reach here when called correctly)
        return $settings->active_wan_port ?? 'ether1';
    }

    // increment correct counter
    private function incrementWanCounter(RouterSetting $settings, string $wan)
    {
        if ($wan === 'ether1') {
            $settings->increment('wan1_current_count');
        } else {
            $settings->increment('wan2_current_count');
        }
    }

    // decrement correct counter (used by scheduled command on expiry)
        public static function decrementWanCounterByPort(int $resellerId, string $wan)
    {
        $settings = RouterSetting::where('reseller_id', $resellerId)->first();
        if (!$settings) return;

        if ($wan === 'ether1' && $settings->wan1_current_count > 0) {
            $settings->decrement('wan1_current_count');
        }

        if ($wan === 'ether2' && $settings->wan2_current_count > 0) {
            $settings->decrement('wan2_current_count');
        }
    }

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

        if ($user->reseller_id != $request->reseller_id) {
            return response()->json([
                'message' => 'Your selected reseller does not match your registered reseller.'
            ], 422);
        }

        $reseller = Reseller::with(['user','router'])->findOrFail($request->reseller_id);
        $profile  = VoucherProfile::findOrFail($request->profile_id);
        $amount   = $profile->price;
        $useCashback = $request->boolean('use_cashback', false);

        $buyerWallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['account_number' => User::generateAccountNumber(), 'balance' => 0]
        );

        /* =======================
           ✅ FIX 1: VALIDATE ONLY
           ======================= */
        if ($useCashback) {
            if ($buyerWallet->cashback_balance < $amount) {
                return response()->json(['message' => 'Insufficient cashback balance.'], 422);
            }
        } else {
            if ($buyerWallet->balance < $amount) {
                return response()->json(['message' => 'Insufficient wallet balance.'], 422);
            }
        }

        // $settings = RouterSetting::firstOrCreate(['reseller_id' => $reseller->id]);
         $settings = RouterSetting::where('reseller_id', $reseller->id)
            ->lockForUpdate()
            ->firstOrCreate(['reseller_id' => $reseller->id]);


        $wan1Full = $settings->wan1_current_count >= $settings->wan1_limit;
        $wan2Full = $settings->wan2_current_count >= $settings->wan2_limit;

        if ($wan1Full && $wan2Full) {

            // respect manual/global sold-out
            if ($settings->global_sold_out_until && now()->lessThan($settings->global_sold_out_until)) {
                return $this->handleSoldOutResponse($settings);
            }

            $expected = $this->calculateExpectedAvailableAt($reseller->id, $profile);

            $position = Waitlist::where('reseller_id', $reseller->id)
                ->where('status', 'waiting')
                ->count() + 1;

            $wait = Waitlist::create([
                'reseller_id' => $reseller->id,
                'user_id' => $user->id,
                'profile_id' => $profile->id,
                'position' => $position,
                'expected_available_at' => $expected,
                'status' => 'waiting',
            ]);

            $user->notify(new \App\Notifications\WaitlistJoined($wait));

            return response()->json([
                'message' => 'All WAN lines are currently sold out.',
                'waitlist' => true,
                'position' => $position,
                'expected_available_at' => $expected->toDateTimeString(),
            ], 429);
        }


        $activeWan = $wan1Full ? 'ether2' : 'ether1';

        try {
            DB::transaction(function () use (
                $reseller,
                $profile,
                $amount,
                $buyerWallet,
                $user,
                $useCashback,
                &$voucher,
                $activeWan,
                $settings
            ) {

                $username = 'V' . strtoupper(Str::random(6));
                $password = Str::random(8);
                $expiresAt = now()->addMinutes((int)$profile->time_minutes);

                $voucher = Voucher::create([
                    'reseller_id' => $reseller->id,
                    'profile_id'  => $profile->id,
                    'code'        => $username,
                    'password'    => $password,
                    'status'      => 'active',
                    'expires_at'  => $expiresAt,
                ]);

                /* =======================
                   ✅ FIX 2: SINGLE DEBIT
                   ======================= */
                if ($useCashback) {
                    $buyerWallet->debitCashback($amount);
                } else {
                    $buyerWallet->debit($amount);
                }


                Transaction::create([
                    'user_id'      => $user->id,
                    'type'         => 'debit',
                    'amount'       => $amount,
                    'status'       => 'success',
                    'reference'    => 'VOUCHER-' . strtoupper(Str::random(10)),
                    'description'  => $useCashback
                        ? 'voucher_purchase_cashback'
                        : 'voucher_purchase',
                    'prev_balance' => $buyerWallet->prev_balance,
                    'new_balance'  => $buyerWallet->new_balance,
                ]);
               


                $resellerWallet = Wallet::firstOrCreate(
                    ['user_id' => $reseller->user->id],
                    ['account_number' => User::generateAccountNumber(), 'balance' => 0]
                );

                $resellerWallet->balance += $amount;
                $resellerWallet->save();

                VoucherQueue::create([
                    'voucher_id' => $voucher->id,
                    'reseller_id' => $reseller->id,
                    'wan_port' => $activeWan,
                    'expiry_time' => $expiresAt,
                ]);

                $activeWan === 'ether1'
                    ? $settings->increment('wan1_current_count')
                    : $settings->increment('wan2_current_count');
    });

      

        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Purchase failed.'], 500);
        }

        return response()->json([
            'success' => true,
            'voucher_id' => $voucher->id,
            'code' => $voucher->code,
            'password' => $voucher->password,
            'expires_at' => $voucher->expires_at->toDateTimeString(),
        ]);
    }



       
        private function getNiceWanName($wan)
        {
            return $wan === 'ether1' ? 'WAN 1' : 'WAN 2';
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
