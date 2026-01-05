<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sewa;

class Kostum extends Model
{
    use HasFactory;

    protected $table = 'kostums';

    protected $fillable = [
        'nama_kostum',
        'kategori',
        'harga',
        'catatan',
        'status',
        'image_kostum'
    ];

    /**
     * Cek apakah kostum sedang dipakai (ada di sewa aktif)
     */
    public function sedangDipakai(): bool
    {
        // status = 0 => SEWA AKTIF
        $sewasAktif = Sewa::where('status', 0)->get();

        foreach ($sewasAktif as $sewa) {

            $kostumIds = json_decode($sewa->kostum_id, true);

            if (!is_array($kostumIds)) {
                continue;
            }

            if (in_array((string) $this->id, $kostumIds, true)) {
                return true;
            }
        }

        return false;
    }
}
