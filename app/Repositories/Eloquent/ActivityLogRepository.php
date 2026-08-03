<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function all($limit = 100)
    {
        return ActivityLog::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function create(array $data)
    {
        return ActivityLog::create($data);
    }

    public function clear()
    {
        return ActivityLog::truncate();
    }
}
