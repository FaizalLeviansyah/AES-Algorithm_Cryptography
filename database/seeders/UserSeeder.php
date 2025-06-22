<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;      // <-- Import model User
use Illuminate\Support\Facades\Hash; // <-- Import Hash

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'fullname' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Ganti 'password' dengan password yg lebih aman
            'level' => 'Admin',
            'division_id' => null, // Admin tidak terikat divisi tertentu
            'status' => '1',
        ]);
    }
}
