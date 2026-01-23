<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primary Super Admin
        User::updateOrCreate(
            ['email' => 'habee2004@gmail.com'],
            [
                'name' => 'Master Super Admin',
                'password' => Hash::make('Zacchaues_01011010'),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
                'is_active' => true,
                'is_approved' => true,
                'is_permanently_blocked' => false,
                'locked_until' => null,
                'failed_login_attempts' => 0,
            ]
        );

        // Additional Super Admin: Raymond Nino Ong
        User::updateOrCreate(
            ['email' => 'whysofunny2003@gmail.com'],
            [
                'name' => 'Raymond Nino Ong',
                'password' => Hash::make('whysofunny2003'),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
                'is_active' => true,
                'is_approved' => true,
                'is_permanently_blocked' => false,
                'locked_until' => null,
                'failed_login_attempts' => 0,
            ]
        );
    }
}
