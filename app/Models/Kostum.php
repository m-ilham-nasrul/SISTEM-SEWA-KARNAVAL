<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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



    public function sewas()
    {
        return $this->hasMany(Sewa::class);
    }

    public function sedangDipakai()
    {
        // Ambil semua sewa yang statusnya aktif
        $sewasAktif = $this->sewas()->where('status', 0)->get();

        foreach ($sewasAktif as $sewa) {
            $kostumIds = is_array($sewa->kostum_id) ? $sewa->kostum_id : json_decode($sewa->kostum_id, true);
            if (in_array($this->id, $kostumIds)) {
                return true;
            }
        }

        return false;
    }
}
