<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telp',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function penyewa()
    {
        return $this->hasOne(\App\Models\Penyewa::class);
    }
}
