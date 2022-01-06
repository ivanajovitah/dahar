<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback',
    ];

    public function generates() {
        return $this->hasMany(Generate::class,'id_resultFeedback','id');
    }

}
