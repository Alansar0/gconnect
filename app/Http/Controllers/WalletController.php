<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\VirtualAccount;
use App\Services\PaymentPointService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Create PaymentPoint virtual account for user
     * (called once per user)
     */
    public function createVirtualAccount(Request $request)
    {
        $user = auth()->user(); // logged-in user

        // Ensure wallet exists
        $wallet = Wallet::firstOrCreate([
            'user_id' => $user->id
        ]);

        // Prevent duplicate virtual accounts
        if ($wallet->virtualAccounts()->where('provider', 'paymentpoint')->exists()) {
            return back()->with('info', 'Virtual account already created.');
        }

        $response = PaymentPointService::createVirtualAccount($user);

        if (($response['status'] ?? null) !== 'success') {
            return back()->with('error', 'Unable to create virtual account.');
        }

        foreach ($response['bankAccounts'] as $bank) {
            VirtualAccount::create([
                'wallet_id'     => $wallet->id,
                'provider'      => 'paymentpoint',
                'bank_name'     => $bank['bankName'],
                'account_number'=> $bank['accountNumber'],
                'account_name'  => $bank['accountName'],
                'provider_ref'  => $bank['Reserved_Account_Id'],
            ]);
        }

        return back()->with('success', 'Bank account created successfully.');
    }

    
        public function acc()
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()]
        );

        $virtualAccounts = $wallet->virtualAccounts()->get();
            
        return view('wallet.accno', compact('wallet', 'virtualAccounts'));
    }

}
