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
        'tanggal_sewa',
        'tanggal_kembali',
        'total_biaya',
        'denda',
        'kondisi',
        'catatan',
        'status',
        'status_bayar',
        'midtrans_order_id',
        'snap_token',              
        'snap_token_created_at',
        'transaction_status',
        'payment_type',   
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'status' => 'integer',
        'status_bayar' => 'boolean',
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
