<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;

class AdminTransactionController extends Controller
{
    
    public function all(Request $request)
    {
        $search = $request->input('search');

        $completed = Transaction::with('user')
            ->whereIn('status', ['success', 'failed'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.transactions.all', compact('completed', 'search'));
    }





        public function processings()
        {
            $processings = Transaction::where('status', 'processing')->get();

            return view('admin.transactions.processings', compact('processings'));
        }

}
