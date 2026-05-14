<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $fillable = [
        'kode', 
        'nama_kriteria', 
        'tipe', 
        'bobot'
    ];
}