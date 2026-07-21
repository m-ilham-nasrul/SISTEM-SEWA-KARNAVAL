<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailSewa;
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

    // relasi ke detail sewa
    public function detailSewas()
    {
        return $this->hasMany(DetailSewa::class);
    }

    // relasi ke sewa (melalui detail)
    public function sewas()
    {
        return $this->belongsToMany(
            Sewa::class,
            'detail_sewas',
            'kostum_id',
            'sewa_id'
        );
    }

    // cek apakah sedang dipakai
    public function sedangDipakai($excludeSewaId = null): bool
    {
        return $this->detailSewas()
            ->whereHas('sewa', function ($q) use ($excludeSewaId) {

                $q->where('status', 0);

                if ($excludeSewaId) {
                    $q->where('id', '!=', $excludeSewaId);
                }
            })
            ->exists();
    }
}
