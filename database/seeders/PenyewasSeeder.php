<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PenyewasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penyewas')->truncate();

        DB::table('penyewas')->insert([
            [
                'id' => 1,
                'user_id' => 2,
                'no_telp' => '087695446780',
                'alamat' => 'Salaman',
                'created_at' => '2026-01-27 01:57:29',
                'updated_at' => '2026-01-27 01:57:29',
                'deleted_at' => null
            ]
        ]);
    }
}