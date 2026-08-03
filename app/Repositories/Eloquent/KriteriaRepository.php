<?php

namespace App\Repositories\Eloquent;

use App\Models\Kriteria;
use App\Repositories\Contracts\KriteriaRepositoryInterface;

class KriteriaRepository implements KriteriaRepositoryInterface
{
    public function all()
    {
        return Kriteria::all();
    }

    public function find($id)
    {
        return Kriteria::findOrFail($id);
    }

    public function create(array $data)
    {
        return Kriteria::create($data);
    }

    public function update($id, array $data)
    {
        $kriteria = $this->find($id);
        $kriteria->update($data);
        return $kriteria;
    }

    public function delete($id)
    {
        $kriteria = $this->find($id);
        return $kriteria->delete();
    }
}
