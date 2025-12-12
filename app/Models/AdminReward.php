<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReward extends Model
{
    protected $table = 'admin_rewards';

    protected $fillable = [
        'for',
        'cashback_amount',
        'voucher_rate',
        'note',
    ];

    public static function getFor(string $type): ?self
    {
        return self::where('for', $type)->first();
    }
}
