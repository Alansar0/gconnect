<?php

namespace App\Http\Controllers;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use App\Models\VoucherProfile;
use App\Models\VirtualAccount;
use App\Models\Reseller;
use App\Models\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
  
    public function dashboard()
{
    $wallet = auth()->user()->wallet;
    $virtualAccount = $wallet->virtualAccounts()->first();


    $transactions = \App\Models\Transaction::where('user_id', auth()->id())
        ->latest()
        ->take(5)
        ->get();


    return view('dashboard', compact('wallet', 'transactions','virtualAccount'));
}


}
