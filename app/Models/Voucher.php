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


    public function reseller(){

        return $this->belongsTo(Reseller::class);

    }

}

