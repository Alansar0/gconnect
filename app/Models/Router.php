<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class Router extends Model {
    protected $guarded = [];

    // store encrypted
    public function setPasswordEncryptedAttribute($value){
        $this->attributes['password_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getPasswordAttribute() {
        return $this->password_encrypted ? Crypt::decryptString($this->password_encrypted) : null;
    }

        protected $casts = [
        'is_online' => 'boolean',
        'active' => 'boolean',
    ];


    public function reseller(){ return $this->belongsTo(Reseller::class); }
}

