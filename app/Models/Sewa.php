<?php

namespace App\Models;

use App\Enums\StatusBayar;
use App\Models\DetailSewa;
use App\Models\Kostum;
use App\Models\Penyewa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'sewas';

    protected $fillable = [
        'kode_sewa',
        'penyewa_id',
        'tanggal_sewa',
        'tanggal_kembali',
        'total_biaya',
        'dp',
        'sisa_bayar',
        'denda',
        'kondisi',
        'catatan',
        'status',
        'status_bayar',
        'midtrans_order_id_dp',
        'midtrans_order_id_pelunasan',
        'snap_token',
        'snap_token_created_at',
        'transaction_status',
        'payment_type',
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'status' => 'integer',
        'status_bayar' => StatusBayar::class,
        'dp' => 'integer',
        'sisa_bayar' => 'integer',
        'denda' => 'integer',
        'snap_token_created_at' => 'datetime',
    ];
    // relasi ke penyewa
    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class);
    }
    // relasi ke detail
    public function details()
    {
        return $this->hasMany(DetailSewa::class);
    }
    // relasi langsung ke kostum (optional helper)
    public function kostums()
    {
        return $this->belongsToMany(
            Kostum::class,
            'detail_sewas',
            'sewa_id',
            'kostum_id'
        );
    }
}
