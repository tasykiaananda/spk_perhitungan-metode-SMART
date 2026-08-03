<?php

namespace App\Repositories\Eloquent;

use App\Models\Alternatif;
use App\Repositories\Contracts\AlternatifRepositoryInterface;

class AlternatifRepository implements AlternatifRepositoryInterface
{
    public function all()
    {
        return Alternatif::all();
    }

    public function find($id)
    {
        return Alternatif::findOrFail($id);
    }

    public function create(array $data)
    {
        return Alternatif::create($data);
    }

    public function update($id, array $data)
    {
        $alternatif = $this->find($id);
        $alternatif->update($data);
        return $alternatif;
    }

    public function delete($id)
    {
        $alternatif = $this->find($id);
        return $alternatif->delete();
    }
}
