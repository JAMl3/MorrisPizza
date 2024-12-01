<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@morrispizza.co.uk',
            'password' => Hash::make('P@ssword'),
            'is_admin' => true,
        ]);
    }
} 