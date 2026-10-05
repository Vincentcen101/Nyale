<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (app()->isProduction() && blank($password)) {
            throw new \RuntimeException('Set ADMIN_PASSWORD in .env before seeding in production.');
        }

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@nyaleinstitute.org.mw')],
            [
                'name' => 'Nyale Institute Admin',
                'password' => Hash::make($password ?: 'password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
