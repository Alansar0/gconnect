<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class VoucherQueue extends Model{
        protected $table = 'voucher_queue';
        protected $guarded = [];
        protected $casts = [
        'expiry_time' => 'datetime',
        ];

        // protected $casts = ['expiry_time' => 'datetime'];


        public function voucher() { return $this->belongsTo(Voucher::class); }

    }
