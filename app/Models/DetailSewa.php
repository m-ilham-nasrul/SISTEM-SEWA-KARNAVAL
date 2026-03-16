<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSewa extends Model
{
    protected $table = 'detail_sewas';

    protected $fillable = [
        'sewa_id',
        'kostum_id',
        'harga'
    ];

    public function sewa()
    {
        return $this->belongsTo(Sewa::class);
    }

    public function kostum()
    {
        return $this->belongsTo(Kostum::class);
    }
}