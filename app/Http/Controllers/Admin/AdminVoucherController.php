<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherProfile;
use App\Models\Reseller;
use App\Models\RouterSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

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
            'name' => 'required|string|max:255',
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


}

