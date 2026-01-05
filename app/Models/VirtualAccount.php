<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    protected $fillable = [
        'wallet_id',
        'provider',
        'bank_name',
        'account_number',
        'account_name',
        'provider_ref'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}


