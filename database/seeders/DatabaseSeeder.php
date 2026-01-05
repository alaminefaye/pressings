<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'phone' => '+2250700000000',
            'email' => 'admin@pressing.com',
            'password' => Hash::make('password'),
            'first_name' => 'Admin',
            'last_name' => 'Pressing',
            'role' => 'admin',
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        // Create employee user
        User::create([
            'phone' => '+2250700000001',
            'email' => 'employee@pressing.com',
            'password' => Hash::make('password'),
            'first_name' => 'Employé',
            'last_name' => 'Test',
            'role' => 'employee',
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        // Create driver user
        User::create([
            'phone' => '+2250700000002',
            'email' => 'driver@pressing.com',
            'password' => Hash::make('password'),
            'first_name' => 'Livreur',
            'last_name' => 'Test',
            'role' => 'driver',
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        // Create test client
        User::create([
            'phone' => '+2250700000003',
            'email' => 'client@pressing.com',
            'password' => null,
            'first_name' => 'Client',
            'last_name' => 'Test',
            'role' => 'client',
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        // Seed other tables
        $this->call([
            ClothingTypeSeeder::class,
            ServiceSeeder::class,
            PriceSeeder::class,
            SettingSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
