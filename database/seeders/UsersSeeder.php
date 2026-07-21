<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Nasrul',
                'email' => 'nasrul@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'penyewa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Muhammad Nasrul',
                'email' => 'nasrulmuhammad@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'penyewa',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
