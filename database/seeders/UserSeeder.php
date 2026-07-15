<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Le Cameleon',
            'email' => 'admin@lecameleon.store',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'phone' => '+1-555-0100',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Staff Curator',
            'email' => 'staff@lecameleon.store',
            'password' => Hash::make('password'),
            'role' => UserRole::Staff,
            'phone' => '+1-555-0101',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Demo Customer',
            'email' => 'customer@lecameleon.store',
            'password' => Hash::make('password'),
            'role' => UserRole::Customer,
            'phone' => '+1-555-0102',
            'is_active' => true,
        ]);
    }
}
