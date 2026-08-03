<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    public function get($key, $default = null);
    public function set($key, $value);
    public function all();
}
