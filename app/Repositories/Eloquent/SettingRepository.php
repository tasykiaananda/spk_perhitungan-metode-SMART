<?php

namespace App\Repositories\Eloquent;

use App\Models\WebsiteSetting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function get($key, $default = null)
    {
        return WebsiteSetting::getByKey($key, $default);
    }

    public function set($key, $value)
    {
        return WebsiteSetting::setByKey($key, $value);
    }

    public function all()
    {
        return WebsiteSetting::pluck('value', 'key')->all();
    }
}
