<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Seed Roles
        $adminRoleId = DB::table('roles')->insertGetId([
            'title' => 'admin',
        ]);

        DB::table('roles')->insert([
            'title' => 'user'
        ]);

        DB::table('users')->insert([
            'role_id' => $adminRoleId,
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make("admin password")
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
