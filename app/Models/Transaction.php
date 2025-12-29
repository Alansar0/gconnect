<?php

// app/Models/Transaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Transaction extends Model
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    // protected $fillable = [
    //     'user_id', 'type', 'amount', 'status', 'reference', 'description',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}

