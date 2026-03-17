<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Resto Cafe Manager',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'cashier@example.com',
        ], [
            'name' => 'Kasir Utama',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'kasir.pagi@example.com',
        ], [
            'name' => 'Kasir Shift Pagi',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'kasir.sore@example.com',
        ], [
            'name' => 'Kasir Shift Sore',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
