<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Account
        |--------------------------------------------------------------------------
        */
        $admin = User::where('email', env('ADMIN_EMAIL'))
            ->orWhere('phone_number', env('ADMIN_PHONE'))
            ->first();

        if (!$admin) {
            User::create([
                'full_name'    => env('ADMIN_NAME', 'Local Admin'),
                'email'        => env('ADMIN_EMAIL', 'admin@example.com'),
                'phone_number' => env('ADMIN_PHONE', '08000000000'),
                'password'     => Hash::make(env('ADMIN_PASSWORD', '@security')),
                'role'         => 'admin',
            ]);

            $this->command->info('✅ Admin user created');
        } else {
            $this->command->warn('⚠️ Admin already exists');
        }

        /*
        |--------------------------------------------------------------------------
        | Default User Account
        |--------------------------------------------------------------------------
        */
        $user = User::where('email', env('USER_EMAIL'))
            ->orWhere('phone_number', env('USER_PHONE'))
            ->first();

        if (!$user) {
            User::create([
                'full_name'    => env('USER_NAME', 'Default User'),
                'email'        => env('USER_EMAIL', 'user@example.com'),
                'phone_number' => env('USER_PHONE', '08099999999'),
                'password'     => Hash::make(env('USER_PASSWORD', 'password123')),
                'role'         => 'user',
            ]);

            $this->command->info('✅ Default user created');
        } else {
            $this->command->warn('⚠️ Default user already exists');
        }
    }
}
