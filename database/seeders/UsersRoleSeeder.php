<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersRoleSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );
        User::updateOrCreate(
            ['email' => 'admin_toko@ayorenne.com'],
            [
                'name' => 'Admin Toko',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'business_type' => 'toko',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir_toko@ayorenne.com'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'business_type' => 'toko',
            ]
        );
    }
}
