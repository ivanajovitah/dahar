<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generate extends Model
{
    use HasFactory;

    protected $fillable = [
        'idUser',
        'forDate',
        'groupMenu',
        'idMenu',
        'nama_resep',
        'calories',
        'carbs',
        'fat',
        'protein',
        'feedback',
        'id_resultFeedback',
    ];


    public function user(){
        return $this->belongsTo(User::class,'idUser','id');
    }

    public function resep(){
        return $this->belongsTo(Resep::class,'idMenu','id');
    }

    public function resultFeedback(){
        return $this->hasOne(ResultFeedback::class,'id_resultFeedback','id');
    }
    
}
