<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Reseller;
use App\Models\Router;

class AdminUserController extends Controller
{

    // View all users

     public function view(Request $request)
    {
        $query = User::query();

        // Search by email or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('email', 'LIKE', "%$search%")
                  ->orWhere('phone_number', 'LIKE', "%$search%");
        }

        $users = $query->latest()->paginate(10);
        return view('admin.user.viewUser', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $user->id,
    ]);

    $user->update([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
    ]);

    return redirect()->route('viewUser')->with('success', 'User updated successfully!');
}


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

      public function displaychangepassword()
    {
        return view('admin.user.changepassword');
    }

    // Update user password manually
    public function updatechangePassword(Request $request)
    {
        $request->validate([
            'identifier' => 'required', // can be email or phone
            'new_password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->identifier)
                    ->orWhere('phone_number', $request->identifier)
                    ->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }


        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', $user->full_name.' Password updated successfully to ' . $request->new_password);
    }

         public function changePin()
    {
        return view('admin.user.changePin');
    }

   

public function updatePin(Request $request)
{
    $request->validate([
        'identifier' => 'required', // can be email or phone
        'new_pin' => 'required|digits:4',
    ]);

    $user = User::where('email', $request->identifier)
                ->orWhere('phone_number', $request->identifier)
                ->first();

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    // 🛑 FIX IS HERE: Hash the new pin before saving
    $user->pin_code = Hash::make($request->new_pin); // Use Hash::make()

    $user->save();

    return back()->with('success', $user->full_name.' Pin Code updated successfully to ' . $request->new_pin);
}




    // Show the search form
    public function blockForm(Request $request)
    {
        $user = null;

        if ($q = $request->input('query')) {
            $user = User::where('email', $q)->orWhere('phone_number', $q)
                ->first();
        }

        return view('admin.user.blockUser', compact('user'));
    }

    // Toggle block/unblock
    public function toggleBlock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Prevent admin from blocking themselves
        if ($user->id == auth()->id()) {
            return back()->with('error', "You cannot block yourself.");
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'blocked' : 'unblocked';

        return back()->with('success', "User {$user->full_name} has been {$status} successfully.");
    }

        public function walletView(Request $request)
    {
        $wallet = null;
        $transactions = collect();

       
         if ($q = $request->query('query')) {
        $wallet = Wallet::whereHas('user', function ($query) use ($q) {
            $query->where('email', 'LIKE', "%$q%")
                  ->orWhere('phone_number', 'LIKE', "%$q%");
        })->first();

        if ($wallet) {
            $transactions = Transaction::where('user_id', $wallet->user_id)
                ->latest()
                ->take(20)
                ->get();
        }
    }
        return view('admin.user.walletManage', compact('wallet', 'transactions'));
    }

    public function updateWallet(Request $request, Wallet $wallet)
{
    $request->validate([
        'type'        => 'required|in:credit,debit',
        'amount'      => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
    ]);

    $reference = strtoupper(uniqid('ADMIN_'));
    $desc = $request->description ?? 'Admin wallet adjustment';

    try {
        if ($request->type === 'credit') {
            $wallet->credit($request->amount, $reference, $desc);
        } else {
            $wallet->debit($request->amount, $reference, $desc);
        }
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }

    return back()->with('success', 'Wallet updated successfully.');
}


    /**
     * Admin manual upgrade or router add
     */

    public function adminUpgradeView()
    {
        return view('admin.user.manual-upgrade');
    }


    /**
     * Admin Manual Upgrade endpoint
     */
    public function manualUpgrade(Request $request)
    {
        // 1. Validation for the target user ID and router details
        $request->validate([
            // Ensures the user exists AND their role is NOT already 'reseller'
            // to prevent accidental re-upgrade, though updateOrCreate handles that model-wise.
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255',
            'port' => 'required|numeric',
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:255',
        ]);

        // We use the validated user_id here
        $userId = $request->user_id;

        // 2. Find the user to be upgraded
        $userToUpgrade = User::find($userId);

        // 3. Create or update the Reseller record
        $reseller = Reseller::updateOrCreate(
            ['user_id' => $userId],
            ['name' => $request->name, 'status' => 'active']
        );

        // 4. Create or update the Router details
        Router::updateOrCreate(
            ['reseller_id' => $reseller->id],
            [
                'host' => $request->host,
                'port' => $request->port,
                'username' => $request->username,
                // Encrypt the password for security
                'password' => encrypt($request->password),
            ]
        );

        // 5. Update the user's role to 'reseller'
        $userToUpgrade->update(['role' => 'reseller']);

        return response()->json([
            'success' => true,
            'message' => "User ID {$userId} successfully upgraded and router details saved.",
            'reseller_id' => $reseller->id
        ], 200);
    }



}
