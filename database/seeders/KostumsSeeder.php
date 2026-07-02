<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KostumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kostums')->delete();

        DB::table('kostums')->insert([
            [
                'id' => 1,
                'nama_kostum' => 'Sengkuni & Durga',
                'image_kostum' => '1769569193_Sengkuni & Durga.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 2000000,
                'status' => 0,
                'created_at' => '2026-01-25 21:10:17',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 2,
                'nama_kostum' => 'Maskot Dewi Ungu',
                'image_kostum' => '1769569214_Maskot Dewi Ungu.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:11:36',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 3,
                'nama_kostum' => 'Maskot Dewi Merah',
                'image_kostum' => '1769569237_Maskot Dewi Merah.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:12:18',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 4,
                'nama_kostum' => 'Maskot Dewi Silver',
                'image_kostum' => '1769569256_Maskot Dewi Silver.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:12:57',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 5,
                'nama_kostum' => 'Maskot Dewi Biru',
                'image_kostum' => '1769569303_Maskot Dewi Biru.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:13:22',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 6,
                'nama_kostum' => 'Maskot Naga',
                'image_kostum' => '1769569343_Maskot Naga.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:14:03',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 7,
                'nama_kostum' => 'Maskot Ksatria Wibawa',
                'image_kostum' => '1769569469_Maskot Ksatria Wibawa.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:15:37',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 8,
                'nama_kostum' => 'Maskot Ksatria Amarah',
                'image_kostum' => '1769569485_Maskot Ksatria Amarah.jpg',
                'kategori' => 'Kostum',
                'catatan' => null,
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:16:39',
                'updated_at' => '2026-01-29 00:01:29',
                'deleted_at' => null
            ],
            [
                'id' => 9,
                'nama_kostum' => 'Maskot Iblis Kream',
                'image_kostum' => '1769569510_Maskot Iblis Kream.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:21:06',
                'updated_at' => '2026-01-27 20:05:10',
                'deleted_at' => null
            ],
            [
                'id' => 10,
                'nama_kostum' => 'Maskot Iblis Merah',
                'image_kostum' => '1769569536_Maskot Iblis Merah.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:22:10',
                'updated_at' => '2026-01-27 20:05:36',
                'deleted_at' => null
            ],
            [
                'id' => 11,
                'nama_kostum' => 'Monster Garuda',
                'image_kostum' => '1769569563_Monster Garuda.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:24:03',
                'updated_at' => '2026-01-27 20:06:03',
                'deleted_at' => null
            ],
            [
                'id' => 12,
                'nama_kostum' => 'Monster Burung Hantu',
                'image_kostum' => '1769569585_Monster Burung Hantu.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:24:48',
                'updated_at' => '2026-01-27 20:06:25',
                'deleted_at' => null
            ],
            [
                'id' => 13,
                'nama_kostum' => 'Monster Engrang Kream',
                'image_kostum' => '1769569613_Monster Engrang Kream.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:26:18',
                'updated_at' => '2026-01-27 20:06:53',
                'deleted_at' => null
            ],
            [
                'id' => 14,
                'nama_kostum' => 'Maskot Engrang Coklat',
                'image_kostum' => '1769569669_Maskot Engrang Coklat.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:26:48',
                'updated_at' => '2026-01-27 20:07:49',
                'deleted_at' => null
            ],
            [
                'id' => 15,
                'nama_kostum' => 'Monster Engrang Merah',
                'image_kostum' => '1769569687_Monster Engrang Merah.jpg',
                'kategori' => 'Full Body',
                'catatan' => null,
                'harga' => 300000,
                'status' => 0,
                'created_at' => '2026-01-25 21:27:19',
                'updated_at' => '2026-01-27 20:08:07',
                'deleted_at' => null
            ],
            [
                'id' => 16,
                'nama_kostum' => 'Prajurit Wira',
                'image_kostum' => '1769569943_Prajurit Wira.jpeg',
                'kategori' => 'Kostum',
                'catatan' => 'Jumlah Kostum Prajurit ada 80 piece',
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:29:21',
                'updated_at' => '2026-01-27 20:12:23',
                'deleted_at' => null
            ],
            [
                'id' => 17,
                'nama_kostum' => 'Prajurit Dyah',
                'image_kostum' => '1769570210_Prajurit Dyah.jpg',
                'kategori' => 'Kostum',
                'catatan' => 'Jumlah Kostum Prajurit ada 80 piece',
                'harga' => 100000,
                'status' => 0,
                'created_at' => '2026-01-25 21:29:52',
                'updated_at' => '2026-01-27 20:16:50',
                'deleted_at' => null
            ],
            [
                'id' => 18,
                'nama_kostum' => 'Bathara Guru',
                'image_kostum' => '1769570232_Bathara Guru.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1500000,
                'status' => 0,
                'created_at' => '2026-01-25 21:30:32',
                'updated_at' => '2026-01-27 20:17:12',
                'deleted_at' => null
            ],
            [
                'id' => 19,
                'nama_kostum' => 'Prabu Siliwangi',
                'image_kostum' => '1769570569_Prabu Siliwangi.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1000000,
                'status' => 0,
                'created_at' => '2026-01-25 21:31:07',
                'updated_at' => '2026-01-27 20:22:49',
                'deleted_at' => null
            ],
            [
                'id' => 20,
                'nama_kostum' => 'Watu Kelir',
                'image_kostum' => '1769570597_Watu Kelir.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1000000,
                'status' => 0,
                'created_at' => '2026-01-25 21:31:40',
                'updated_at' => '2026-01-27 20:23:17',
                'deleted_at' => null
            ],
            [
                'id' => 21,
                'nama_kostum' => 'Gatotkaca',
                'image_kostum' => '1769570636_Gatotkaca.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1000000,
                'status' => 0,
                'created_at' => '2026-01-25 21:47:31',
                'updated_at' => '2026-01-27 20:23:56',
                'deleted_at' => null
            ],
            [
                'id' => 22,
                'nama_kostum' => 'Ramayana',
                'image_kostum' => '1769570705_Ramayana.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1000000,
                'status' => 0,
                'created_at' => '2026-01-25 21:49:31',
                'updated_at' => '2026-01-27 20:25:05',
                'deleted_at' => null
            ],
            [
                'id' => 23,
                'nama_kostum' => 'Bhuta Wana Raja',
                'image_kostum' => '1769738955_Bhuta Wana Raja.jpg',
                'kategori' => 'Ogoh-Ogoh',
                'catatan' => null,
                'harga' => 1000000,
                'status' => 0,
                'created_at' => '2026-01-29 19:09:15',
                'updated_at' => '2026-01-29 19:09:15',
                'deleted_at' => null
            ]
        ]);
    }
}
