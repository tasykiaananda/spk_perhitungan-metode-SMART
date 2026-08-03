<?php

namespace App\Repositories\Eloquent;

use App\Models\Penilaian;
use App\Repositories\Contracts\PenilaianRepositoryInterface;

class PenilaianRepository implements PenilaianRepositoryInterface
{
    public function all()
    {
        return Penilaian::all();
    }

    public function findByAlternatif($alternatifId)
    {
        return Penilaian::where('alternatif_id', $alternatifId)->get();
    }

    public function updateOrCreate($alternatifId, $kriteriaId, $nilai)
    {
        return Penilaian::updateOrCreate(
            [
                'alternatif_id' => $alternatifId,
                'kriteria_id' => $kriteriaId,
            ],
            ['nilai' => $nilai]
        );
    }

    public function deleteByAlternatif($alternatifId)
    {
        return Penilaian::where('alternatif_id', $alternatifId)->delete();
    }

    public function deleteByKriteria($kriteriaId)
    {
        return Penilaian::where('kriteria_id', $kriteriaId)->delete();
    }
}
