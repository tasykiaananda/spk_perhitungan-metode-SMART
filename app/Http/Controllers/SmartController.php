<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmartService;
use App\Repositories\Contracts\HistoryRepositoryInterface;
use App\Models\ActivityLog;
use Carbon\Carbon;

class SmartController extends Controller
{
    protected $smartService;
    protected $historyRepo;

    public function __construct(SmartService $smartService, HistoryRepositoryInterface $historyRepo)
    {
        $this->smartService = $smartService;
        $this->historyRepo = $historyRepo;
    }

    /**
     * Show step-by-step SMART calculation results.
     */
    public function index()
    {
        $result = $this->smartService->calculate();
        return view('admin.smart.index', $result);
    }

    /**
     * Trigger calculation process and save to history.
     */
    public function process(Request $request)
    {
        $result = $this->smartService->calculate();

        if (count($result['rankings']) === 0) {
            return redirect()->route('admin.smart.index')->with('error', 'Tidak ada data supplier atau penilaian untuk diproses.');
        }

        $bestSupplier = $result['rankings'][0]['nama'];
        $bestScore = $result['rankings'][0]['skor'];
        $supplierCount = count($result['nilai_matrix']);
        $kriteriaCount = count($result['kriterias']);

        $now = Carbon::now();

        $this->historyRepo->create([
            'tanggal' => $now->toDateString(),
            'waktu' => $now->toTimeString(),
            'jumlah_supplier' => $supplierCount,
            'jumlah_kriteria' => $kriteriaCount,
            'supplier_terbaik' => $bestSupplier,
            'skor_tertinggi' => $bestScore,
        ]);

        ActivityLog::log("Melakukan proses perhitungan SMART (Supplier Terbaik: {$bestSupplier}, Skor: " . number_format($bestScore, 2) . ")");

        return redirect()->route('admin.smart.index')->with('success', 'Perhitungan SMART berhasil diproses dan disimpan ke riwayat!');
    }

    /**
     * Show detail page for a specific supplier.
     */
    public function detail($id)
    {
        $detail = $this->smartService->getDetail($id);
        return view('admin.supplier.detail', $detail);
    }

    /**
     * Show report print view.
     */
    public function report()
    {
        $result = $this->smartService->calculate();
        return view('admin.smart.report', $result);
    }

    /**
     * Export rankings to Excel.
     */
    public function exportExcel()
    {
        $result = $this->smartService->calculate();
        $rankings = $result['rankings'];
        
        $fileName = 'laporan_perangkingan_smart_' . date('Y-m-d_H-i-s') . '.xls';
        
        ActivityLog::log("Mengekspor laporan hasil perangkingan ke Excel");

        return response()->view('admin.smart.excel', compact('rankings'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
