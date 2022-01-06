<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'judul_resep',
        'yield',
        'bahan',
        'bahanLines',
        'langkah',
        'likes',
        'cover',
        'healthLabels',
        'calories',
        'totalWeight',
        'totalTime',
        'cuisineType',
        'mealType',
        'totalNutrients',
        'totalDaily',
        'digest',
        'score',
        'idAuthor',
    ];

    public function generates() {
        return $this->hasMany(Generate::class,'idMenu','id');
    }

    public function author(){
        return $this->belongsTo(User::class,'idAuthor','id');
    }
}
