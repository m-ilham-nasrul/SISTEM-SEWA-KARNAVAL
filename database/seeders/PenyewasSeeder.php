<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenyewasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('penyewas')->delete();

        DB::table('penyewas')->insert([
            [
                'id' => 1,
                'user_id' => 2,
                'no_telp' => '087695446780',
                'alamat' => 'Salaman',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}