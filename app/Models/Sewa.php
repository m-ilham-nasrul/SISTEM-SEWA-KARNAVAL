<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kostum;

class Sewa extends Model
{
    use HasFactory;

    protected $table = 'sewas';
    protected $fillable = [
        'kode_sewa',
        'penyewa_id',
        'kostum_id',
        'tanggal_sewa',
        'tanggal_kembali',
        'total_biaya',
        'catatan',
        'status',
        'status_bayar',
        'denda',
        'metode_pembayaran',
        'no_rekening',
        'nama_bank',
        'nama_ewallet',
        'nomor_ewallet'
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'status' => 'boolean',
        'status_bayar' => 'boolean',
        'kostum_id' => 'json', // penting
    ];

    // relasi ke penyewa
    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class);
    }

    // accesor untuk mengambil banyak kostum
    public function getKostumListAttribute()
    {
        if (!$this->kostum_id) {
            return collect([]);
        }

        $ids = is_array($this->kostum_id)
            ? $this->kostum_id
            : json_decode($this->kostum_id, true);

        return Kostum::whereIn('id', $ids)->get();
    }
    
}
