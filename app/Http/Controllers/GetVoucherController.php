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
        if ($wan === 'ether1') {
            $settings->decrement('wan1_current_count');
        } else {
            $settings->decrement('wan2_current_count');
        }
    }

    /***** FINAL STORE (purchase attempt) *****/
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

    $reseller = Reseller::with(['user','router'])->findOrFail($request->reseller_id);
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

    // load settings (do NOT force default limits; admin will configure them)
    $settings = RouterSetting::firstOrCreate(['reseller_id' => $reseller->id]);

    // clear expired manual sold-out if it's passed (backward-compatibility)
    if ($settings->global_sold_out_until && now()->greaterThan($settings->global_sold_out_until)) {
        $settings->update(['global_sold_out_until' => null]);
    }


    // determine if both WANs full using admin-set limits
    $wan1Count = (int)$settings->wan1_current_count;
    $wan2Count = (int)$settings->wan2_current_count;
    $wan1Limit = (int)$settings->wan1_limit;
    $wan2Limit = (int)$settings->wan2_limit;

    $bothFull = ($wan1Count >= $wan1Limit) && ($wan2Count >= $wan2Limit);

    // if ($bothFull) {
    //     // compute expected available time
    //     $expected = $this->calculateExpectedAvailableAt($reseller->id, $profile);

    //     $position = Waitlist::where('reseller_id', $reseller->id)
    //         ->where('status','waiting')->count() + 1;

    //     $wait = Waitlist::create([
    //         'reseller_id' => $reseller->id,
    //         'user_id' => $user->id,
    //         'profile_id' => $profile->id,
    //         'position' => $position,
    //         'expected_available_at' => $expected,
    //         'status' => 'waiting',
    //     ]);

    //     // notify (in-app only on join)
    //     $user->notify(new \App\Notifications\WaitlistJoined($wait));

    //     return response()->json([   
    //         'message' => 'All WAN lines are currently sold out.',
    //         'position' => $position,
    //         'expected_available_at' => $expected->toDateTimeString(),
    //     ], 429);
    // }
    if ($bothFull) {

    // 1. Create expiry time based on profile duration
    $minutes = (int) trim($profile->time_minutes);
    $expiresAt = now()->addMinutes($minutes);

    // 2. Set the entire system to "sold out"
    $this->enterSoldOutState($settings, $expiresAt);

    // 3. Add customer to the waitlist
    $expected = $this->calculateExpectedAvailableAt($reseller->id, $profile);

    $position = Waitlist::where('reseller_id', $reseller->id)
        ->where('status','waiting')->count() + 1;

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
        'position' => $position,
        'expected_available_at' => $expected->toDateTimeString(),
        'sold_out_until' => $expiresAt->toDateTimeString(),
    ], 429);
}


    // choose WAN deterministically (wan1 first)
    $activeWan = ($wan1Count < $wan1Limit) ? 'ether1' : 'ether2';

    // proceed to create voucher within DB transaction
    $connection = [
        'host' => $reseller->router->host,
        'port' => $reseller->router->port ?? 8728,
        'username' => $reseller->router->username,
        'password' => $reseller->router->password,
    ];

    try {
        DB::transaction(function () use ($connection, $reseller, $profile, $amount, $buyerWallet, $user, &$voucher, $activeWan, $settings) {
            if (!$this->routerService->connect($connection)) {
                throw new \Exception('Router connection failed.');
            }

            $username = 'V' . strtoupper(Str::random(6));
            $password = Str::random(8);

            $resp = $this->routerService->createVoucher([
                'username' => $username,
                'password' => $password,
                'profile'  => $profile->mikrotik_profile,
            ]);
            if (isset($resp['error'])) {
                throw new \Exception('Router error: ' . $resp['error']);
            }

            // $expiresAt = now()->addMinutes((int)$profile->time_minutes);
            $minutes = (int) trim($profile->time_minutes);
            $expiresAt = now()->addMinutes($minutes);



            $voucher = Voucher::create([
                'reseller_id' => $reseller->id,
                'profile_id'  => $profile->id,
                'code'        => $username,
                'password'    => $password,
                'status'      => 'active',
                'expires_at'  => $expiresAt,
            ]);

            // debit buyer
            $buyerWallet->balance -= $amount;
            $buyerWallet->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'status' => 'success',
                'reference' => 'VOUCHER-' . strtoupper(Str::random(10)),
                'description' => 'voucher_purchase',
            ]);

            // credit reseller
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

            // add to voucher_queue using profile expiry
            VoucherQueue::create([
                'voucher_id' => $voucher->id,
                'reseller_id' => $reseller->id,
                'wan_port' => $activeWan,
                'expiry_time' => $expiresAt,
            ]);

            // increment chosen WAN counter
            if ($activeWan === 'ether1') {
                $settings->increment('wan1_current_count');
            } else {
                $settings->increment('wan2_current_count');
            }
        });
    } catch (\Throwable $e) {
        report($e);
        return response()->json(['message' => 'Purchase failed: ' . $e->getMessage()], 500);
    }

    return response()->json([
        'success' => true,
        'voucher_id' => $voucher->id,
        'code' => $voucher->code,
        'password' => $voucher->password,
        'expires_at' => $voucher->expires_at->toDateTimeString(),
        'receipt_url' => route('getVoucher.receipt', $voucher->id),
    ], 200);
}



        private function enterSoldOutState(RouterSetting $settings,Carbon $expiresAt)
        {
            
            $settings->update([
                'global_sold_out_until' => $expiresAt,
                'wan1_current_count' => 0,
                'wan2_current_count' => 0,
            ]);
        }

        private function handleSoldOutResponse(RouterSetting $settings)
        {
            $remaining = now()->diffForHumans($settings->global_sold_out_until, true);

            return response()->json([
                'message' => 'All WAN lines are currently sold out.',
                'retry_after' => $remaining,
                'sold_out_until' => $settings->global_sold_out_until->toDateTimeString(),
            ], 429);
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
