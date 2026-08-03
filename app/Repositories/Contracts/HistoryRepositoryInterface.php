<?php

namespace App\Repositories\Contracts;

interface HistoryRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function delete($id);
    public function clear();
}
