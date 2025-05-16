<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\Categories;
use App\Models\Favorite;
use App\Models\orders;
use App\Models\Payments;
use App\Models\products;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
// Admin user (only if not exists)
        User::firstOrCreate(
            ['email' => 'admin@760.com'],
            ['name' => 'Admin', 'password' => bcrypt('admin123')]
        );
        // other users
        User::factory(10)->create();
        // Brands::factory(10)->create();

    }
}
