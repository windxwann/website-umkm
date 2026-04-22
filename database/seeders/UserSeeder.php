<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dapoercemalcemil.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@dapoercemalcemil.com',
            'password' => Hash::make('kasir123'),
            'role' => 'cashier',
            'phone' => '081234567891',
            'is_active' => true
        ]);
    }
}