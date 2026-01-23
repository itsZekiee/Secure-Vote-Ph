<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'habee2004@gmail.com'],
            [
                'name' => 'Master Super Admin',
                'password' => Hash::make('Zacchaues_01011010'),
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
                'is_approved' => true,
            ]
        );
    }
}
