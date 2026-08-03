<?php

namespace App\Repositories\Contracts;

interface ActivityLogRepositoryInterface
{
    public function all($limit = 100);
    public function create(array $data);
    public function clear();
}
