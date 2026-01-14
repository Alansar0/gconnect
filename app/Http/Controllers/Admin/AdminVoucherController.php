<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherProfile;
use App\Models\Reseller;
use App\Models\RouterSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class AdminVoucherController extends Controller
{

        public function selectReseller()
    {
        $resellers = Reseller::all();
        return view('admin.VoucherSettings.select', compact('resellers'));
    }


    public function index()
    {
        $profiles = VoucherProfile::orderByDesc('id')->get();
        return view('admin.VoucherSettings.addVoucherPlan', compact('profiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mikrotik_profile' => 'required|string|max:255',
            'time_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        VoucherProfile::create($request->only('name','mikrotik_profile','time_minutes','price') + ['status'=>'active']);
        return back()->with('success', 'Voucher profile created successfully!');
    }

    public function update(Request $request, $id)
    {
        $profile = VoucherProfile::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('voucher_profiles', 'name')->ignore($profile->id),
            ],
            'mikrotik_profile' => 'required|string|max:255',
            'time_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $profile->update($request->all());
        return back()->with('success', 'Voucher profile updated successfully!');
    }

    public function destroy($id)
    {
        VoucherProfile::findOrFail($id)->delete();
        return back()->with('success', 'Voucher profile deleted.');
    }



    // Show the form
    public function viewWanPort(Reseller $reseller)
{
    $settings = RouterSetting::firstOrCreate(
        ['reseller_id' => $reseller->id],
        [
            'wan1_limit' => 0,
            'wan2_limit' => 0,
            'wan1_current_count' => 0,
            'wan2_current_count' => 0,
        ]
    );

    
    // FIX 1: Determine the live active WAN
    $liveActiveWan = ($settings->wan1_current_count < $settings->wan1_limit)
                        ? 'ether1'
                        : 'ether2';

    return view('admin.VoucherSettings.addWanPort', compact('reseller', 'settings', 'liveActiveWan'));
}


    // Update limits / active WAN / sold_out_until
    public function addWanPort(Request $request, Reseller $reseller)
    {
        $data = $request->validate([
            'wan1_limit' => 'required|integer|min:0',
            'wan2_limit' => 'required|integer|min:0',
            'global_sold_out_until' => 'nullable|date',
        ]);

        $settings = RouterSetting::firstOrCreate(['reseller_id' => $reseller->id]);

        // If admin enters empty sold_out_until, clear it
        if (empty($data['global_sold_out_until'])) {
            $data['global_sold_out_until'] = null;
        }

        // Save to DB
        $settings->update($data);


        return redirect()->route('VoucherSettings.addWanPort', $reseller->id)
            ->with('success', 'Router settings updated.');
    }

    // Reset counters (keeps limits intact)
    public function resetCounts(Request $request, Reseller $reseller)
    {
        $settings = RouterSetting::firstOrCreate(['reseller_id' => $reseller->id]);

        $settings->update([
            'wan1_current_count' => 0,
            'wan2_current_count' => 0,
            'global_sold_out_until' => null,
        ]);

        return redirect()->route('VoucherSettings.addWanPort', $reseller->id)
            ->with('success', 'WAN counters reset.');
    }

    public function online()
    {
        $resellers = Reseller::whereHas('router', function ($q) {
            $q->where('is_online', true);
        })
        ->with(['routers' => function ($q) {
            $q->where('is_online', true);
        }])
        ->orderBy('name')
        ->get();

        return view('admin.VoucherSettings.routers_status', compact('resellers'));
    }

    
    // List all resellers
    public function CommissionIndex()
    {
        $resellers = Reseller::with('user')->get();
        return view('admin.VoucherSettings.commissionIndex', compact('resellers'));
    }

    // Show edit form
    public function CommissionEdit(Reseller $reseller)
    {
        return view('admin.VoucherSettings.commissionEdit', compact('reseller'));
    }

    // Update reseller
    public function CommissionUpdate(Request $request, Reseller $reseller)
    {
        $request->validate([
            'commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $reseller->commission_percent = $request->commission_percent;
        $reseller->save();

        return redirect()
            ->route('admin.Commission.index')
            ->with('success', 'Reseller commission updated.');
    }





}

