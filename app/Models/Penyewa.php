<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewas';
    protected $fillable = [
        'user_id',
        'alamat',
        'no_telp',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke sewa
    public function sewas()
    {
        return $this->hasMany(Sewa::class);
    }
}
