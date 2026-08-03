<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = ['alternatif_id', 'kriteria_id', 'nilai'];
}
