<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class AdminTransactionController extends Controller
{
    public function all(Request $request)
{
    $search = $request->input('search');

    $completed = Transaction::with('user')
        ->whereIn('status', ['success', 'failed'])
        ->when($search, function ($query, $search) {
            // Search user phone number
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%");
            })
            // Or search the phone number within description
            ->orWhere('description', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(15);

    return view('admin.transactions.all', compact('completed', 'search'));
}




        public function processings()
        {
            $processings = Transaction::where('status', 'processing')->get();

            return view('admin.transactions.processings', compact('processings'));
        }

}
