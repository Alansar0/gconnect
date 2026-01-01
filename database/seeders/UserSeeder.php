<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        $admin = User::where('email', env('ADMIN_EMAIL'))
            ->orWhere('phone_number', env('ADMIN_PHONE'))
            ->first();

        if (!$admin) {
            User::create([
                'uuid'           => \Ramsey\Uuid\Uuid::uuid4()->toString(), // required
                'full_name'      => env('ADMIN_NAME', 'Local Admin'),
                'email'          => env('ADMIN_EMAIL'),
                'phone_number'   => env('ADMIN_PHONE'),
                'password'       => Hash::make(env('ADMIN_PASSWORD')),
                // 'pin_code'       => Hash::make(env('ADMIN_PIN', '1234')),
                'role'           => 'admin',
                'is_super_admin' => true,
            ]);


            $this->command->info('✅ Admin user created');
        } else {
            $this->command->warn('⚠️ Admin already exists');
        }

        // Default user account
        $user = User::where('email', env('USER_EMAIL'))
            ->orWhere('phone_number', env('USER_PHONE'))
            ->first();

        if (!$user) {
            User::create([
                'uuid'         => Uuid::uuid4()->toString(),
                'full_name'    => env('USER_NAME', 'Default User'),
                'email'        => env('USER_EMAIL', 'user@example.com'),
                'phone_number' => env('USER_PHONE', '08099999999'),
                'password'     => Hash::make(env('USER_PASSWORD', 'password123')),
                // 'pin_code'     => Hash::make(env('USER_PIN', '1234')), // optional default PIN
                'role'         => 'user',
            ]);

            $this->command->info('✅ Default user created');
        } else {
            $this->command->warn('⚠️ Default user already exists');
        }
    }
}
