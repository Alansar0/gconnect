<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expected_available_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function profile() { return $this->belongsTo(VoucherProfile::class, 'profile_id'); }
    
    //     public function edit($resellerId)
    // {
    //     $reseller = Reseller::findOrFail($resellerId);
    //     $settings = $reseller->routerSettings;

    //     $waitingCount = Waitlist::where('reseller_id', $reseller->id)
    //         ->where('status', 'waiting')
    //         ->count();

    //     $nextWait = Waitlist::where('reseller_id', $reseller->id)
    //         ->where('status', 'waiting')
    //         ->orderBy('expected_available_at')
    //         ->first();

    //     $liveActiveWan = $settings->active_wan_port;

    //     return view('admin.router-settings.edit', compact(
    //         'reseller',
    //         'settings',
    //         'waitingCount',
    //         'nextWait',
    //         'liveActiveWan'
    //     ));
    // }

}
