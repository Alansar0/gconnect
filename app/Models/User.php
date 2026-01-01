<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\Models\WebAuthnCredential;
use Laragear\WebAuthn\WebAuthnData;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class User extends Authenticatable implements WebAuthnAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   public function webAuthnCredentials(): MorphMany
{
    return $this->morphMany(WebAuthnCredential::class, 'authenticatable');
}

public function webAuthnId(): UuidInterface
{
    return Uuid::fromString($this->uuid);
}


public function webAuthnData(): WebAuthnData
{
    return new WebAuthnData(
        $this->webAuthnId(),
        $this->email,
        $this->name ?? $this->email
    );
}


public function flushCredentials(string ...$except): void
{
    $this->webAuthnCredentials()
         ->whereNotIn('id', $except)
         ->delete();
}

public function disableAllCredentials(string ...$except): void
{
    $this->webAuthnCredentials()
         ->whereNotIn('id', $except)
         ->delete();
}

public function makeWebAuthnCredential(array $attributes = []): WebAuthnCredential
{
    return $this->webAuthnCredentials()->create($attributes);
}

public function allowsWebAuthn(): bool
{
    return true;
}

    /**
     * Fillable/guarded attributes
     */
    protected $guarded = ['is_super_admin'];

    // Roles constants
    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            Wallet::create([
                'user_id' => $user->id,
                'account_number' => self::generateAccountNumber(),
                'balance' => 0,
            ]);
        });

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = Uuid::uuid4()->toString();
            }
             });
    }

    public static function generateAccountNumber()
    {
        do {
            $account = '66' . mt_rand(10000000, 99999999);
        } while (Wallet::where('account_number', $account)->exists());
        return $account;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_joined' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class)
                    ->where('status', 'active');
    }   
}

// namespace App\Models;

// // use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

// class User extends Authenticatable
// {
//     /** @use HasFactory<\Database\Factories\UserFactory> */
//     use HasApiTokens, HasFactory, Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $guarded = ['is_super_admin'];


//       // If you want constants to avoid magic strings
//     public const ROLE_USER  = 'user';
//     public const ROLE_ADMIN = 'admin';

//     public function isAdmin(): bool
//     {
//         return $this->role === self::ROLE_ADMIN;
//     }

//     public function wallet()
//     {
//         return $this->hasOne(Wallet::class);
//     }

//             protected static function booted()
//         {
//             static::created(function ($user) {
//                 Wallet::create([
//                     'user_id' => $user->id,
//                     'account_number' => self::generateAccountNumber(),
//                     'balance' => 0,
//                 ]);
//             });
//         }

//         public static function generateAccountNumber()
//         {
//             do {
//                 $account = '66' . mt_rand(10000000, 99999999);
//             } while (Wallet::where('account_number', $account)->exists());
//             return $account;
//         }



//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var list<string>
//      */
//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /**
//      * Get the attributes that should be cast.
//      *
//      * @return array<string, string>
//      */
//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'date_joined' => 'datetime', // Assuming you have a custom 'date_joined' column
//             'password' => 'hashed',
//         ];
//     }

//     public function reseller()
//     {
//         return $this->belongsTo(Reseller::class)
//                     ->where('status', 'active');
//     }

    

// }
