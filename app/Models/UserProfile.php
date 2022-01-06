<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'idUser',
        'tinggi_badan',
        'berat_badan',
        'orientasi_makanan',
        'tingkat_aktivitas',
        'kota',
        'provinsi',
    ];

    
}
