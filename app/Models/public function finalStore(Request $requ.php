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

    // load or create router settings for this reseller
    $settings = RouterSetting::firstOrCreate(
        ['reseller_id' => $reseller->id],
        ['wan1_limit' => 0, 'wan2_limit' => 0, 'active_wan_port' => 'ether1']
    );

    // sold-out guard
    if ($settings->global_sold_out_until && now()->lessThan($settings->global_sold_out_until)) {
        return $this->handleSoldOutResponse($settings);
    }

    $activeWan   = $settings->active_wan_port;
    $currentCount = $settings->{$activeWan . '_current_count'};
    $limit        = $settings->{$activeWan . '_limit'};

    // If active reached limit -> attempt switch
    if ($currentCount >= $limit) {
        $other = $activeWan === 'ether1' ? 'ether2' : 'ether1';
        $otherCount = $settings->{$other . '_current_count'};
        $otherLimit = $settings->{$other . '_limit'};

        if ($otherCount < $otherLimit) {
            // switch in DB
            $settings->update(['active_wan_port' => $other]);
            $activeWan = $other;
        } else {
            // both full
            $this->enterSoldOutState($settings);
            return $this->handleSoldOutResponse($settings);
        }
    }

    // proceed to create voucher
    $connection = [
        'host'     => $reseller->router->host,
        'port'     => $reseller->router->port ?? 8728,
        'username' => $reseller->router->username,
        'password' => $reseller->router->password, // ensure model returns decrypted password
    ];

    $voucher = null;

    try {
        DB::transaction(function () use ($reseller, $profile, $amount, $connection, $buyerWallet, $user, $settings, &$voucher, $activeWan) {
            // connect to router
            if (!$this->routerService->connect($connection)) {
                throw new \Exception('Router connection failed.');
            }

            // create credentials
            $username = 'V' . strtoupper(Str::random(6));
            $password = Str::random(8);

            // create voucher on router (mock or live service)
            $resp = $this->routerService->createVoucher([
                'username' => $username,
                'password' => $password,
                'profile'  => $profile->mikrotik_profile,
            ]);

            if (isset($resp['error'])) {
                throw new \Exception('Router error: ' . $resp['error']);
            }

            // save voucher in DB
            $voucher = Voucher::create([
                'reseller_id' => $reseller->id,
                'profile_id'  => $profile->id,
                'code'        => $username,
                'password'    => $password,
                'status'      => 'active',
            ]);

            // debit buyer wallet
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

            // credit reseller wallet
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

            // queue the voucher slot (4 hours expiry)
            VoucherQueue::create([
                'voucher_id' => $voucher->id,
                'reseller_id' => $reseller->id,
                'wan_port' => $activeWan,
                'expiry_time' => now()->addHours(4),
            ]);

            // increment counter for active WAN
            $settings->increment($activeWan . '_current_count');
        });
    } catch (\Throwable $e) {
        // log the error and return a clear JSON message
        report($e);
        return response()->json(['message' => 'Purchase failed: ' . $e->getMessage()], 500);
    }

    // success response
    return response()->json([
        'success' => true,
        'voucher_id' => $voucher->id,
        'code' => $voucher->code,
        'password' => $voucher->password,
        'receipt_url' => route('getVoucher.receipt', $voucher->id),
    ], 200);
}


private function enterSoldOutState(RouterSetting $settings)
{
    $settings->update([
        'global_sold_out_until' => now()->addHours(4),
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
