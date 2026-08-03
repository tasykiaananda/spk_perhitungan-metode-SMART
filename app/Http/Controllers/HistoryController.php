<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\HistoryRepositoryInterface;
use App\Models\ActivityLog;

class HistoryController extends Controller
{
    protected $historyRepo;

    public function __construct(HistoryRepositoryInterface $historyRepo)
    {
        $this->historyRepo = $historyRepo;
    }

    public function index()
    {
        $histories = $this->historyRepo->all();
        return view('admin.history.index', compact('histories'));
    }

    public function destroy($id)
    {
        $this->historyRepo->delete($id);
        ActivityLog::log("Menghapus item riwayat perhitungan ID {$id}");
        return redirect()->route('admin.history.index')->with('success', 'Riwayat berhasil dihapus!');
    }

    public function clear()
    {
        $this->historyRepo->clear();
        ActivityLog::log("Mengosongkan semua riwayat perhitungan");
        return redirect()->route('admin.history.index')->with('success', 'Seluruh riwayat berhasil dihapus!');
    }
}
