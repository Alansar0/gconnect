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
}
