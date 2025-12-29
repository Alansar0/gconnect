<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Reseller;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {

    protected $guarded = [];
    
    protected $casts = [
    'expires_at' => 'datetime',
    ];
     public function minutesLeft(): int
    {
        return now()->diffInMinutes($this->expires_at, false);
    }

    public function isExpired(): bool
    {
        return now()->gte($this->expires_at);
    }

        // App\Models\Voucher.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reseller(){

        return $this->belongsTo(Reseller::class);

    }

}

