<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'nama_instansi',
        'kapolres',
        'foto_kapolres',
        'struktur_organisasi',
        'alamat',
        'telepon',
        'email',
        'jam_pelayanan',
        'visi',
        'misi',
        'sejarah',
        'sambutan',
    ];

    protected $casts = [
        'misi' => 'array',
    ];

}