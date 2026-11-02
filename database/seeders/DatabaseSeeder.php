<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles and permissions exist before any account can be given one.
        $this->call(RolePermissionSeeder::class);

        // Accounts, records and registrations for the demonstration data.
        $this->call(DemoDataSeeder::class);
    }
}
