<?php

namespace App\Repositories\Contracts;

interface PenilaianRepositoryInterface
{
    public function all();
    public function findByAlternatif($alternatifId);
    public function updateOrCreate($alternatifId, $kriteriaId, $nilai);
    public function deleteByAlternatif($alternatifId);
    public function deleteByKriteria($kriteriaId);
}
