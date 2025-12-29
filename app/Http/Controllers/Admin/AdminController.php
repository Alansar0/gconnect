<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Router;
use App\Models\Reseller;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Total registered users
        $totalUsers = User::count();

        // 2. Total users wallet balance
        $totalUserBalance = Wallet::sum('balance');

        // 3. Total funding (all money ever credited successfully)
        $totalFunding = Transaction::where('type', 'credit')
            ->where('status', 'success')
            ->sum('amount');

        // 4. Processing transactions count
        $processingTransactions = Transaction::where('status', 'processing')->count();
        $onlineRouters = \App\Models\Reseller::whereHas('router', function ($q) {
        $q->where('is_online', true);
        })->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalUserBalance',
            'totalFunding',
            'processingTransactions',
            'onlineRouters'
        ));
    }
}
