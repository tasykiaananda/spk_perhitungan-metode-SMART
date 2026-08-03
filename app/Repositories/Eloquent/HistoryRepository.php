<?php

namespace App\Repositories\Eloquent;

use App\Models\CalculationHistory;
use App\Repositories\Contracts\HistoryRepositoryInterface;

class HistoryRepository implements HistoryRepositoryInterface
{
    public function all()
    {
        return CalculationHistory::orderBy('created_at', 'desc')->get();
    }

    public function create(array $data)
    {
        return CalculationHistory::create($data);
    }

    public function delete($id)
    {
        $history = CalculationHistory::findOrFail($id);
        return $history->delete();
    }

    public function clear()
    {
        return CalculationHistory::truncate();
    }
}
