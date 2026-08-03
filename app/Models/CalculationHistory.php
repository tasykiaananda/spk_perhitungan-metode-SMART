<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationHistory extends Model
{
    protected $fillable = [
        'tanggal',
        'waktu',
        'jumlah_supplier',
        'jumlah_kriteria',
        'supplier_terbaik',
        'skor_tertinggi',
    ];
}
