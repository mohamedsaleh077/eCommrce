<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Seed Roles
        DB::table('roles')->insert([
            'title' => 'admin',
        ]);

        DB::table('roles')->insert([
            'title' => 'user'
        ]);

        // Seed Categories
        DB::table('categories')->insert([
            'category' => 'laptops'
        ]);
        DB::table('categories')->insert([
            'category' => 'clothes'
        ]);
        DB::table('categories')->insert([
            'category' => 'phones'
        ]);
        DB::table('categories')->insert([
            'category' => 'accessories'
        ]);
        DB::table('categories')->insert([
            'category' => 'watches'
        ]);

    }
}
