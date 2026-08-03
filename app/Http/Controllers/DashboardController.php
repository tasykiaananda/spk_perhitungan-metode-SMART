<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\KriteriaRepositoryInterface;
use App\Repositories\Contracts\AlternatifRepositoryInterface;
use App\Repositories\Contracts\PenilaianRepositoryInterface;
use App\Repositories\Contracts\HistoryRepositoryInterface;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Services\SmartService;

class DashboardController extends Controller
{
    protected $kriteriaRepo;
    protected $alternatifRepo;
    protected $penilaianRepo;
    protected $historyRepo;
    protected $activityLogRepo;
    protected $smartService;

    public function __construct(
        KriteriaRepositoryInterface $kriteriaRepo,
        AlternatifRepositoryInterface $alternatifRepo,
        PenilaianRepositoryInterface $penilaianRepo,
        HistoryRepositoryInterface $historyRepo,
        ActivityLogRepositoryInterface $activityLogRepo,
        SmartService $smartService
    ) {
        $this->kriteriaRepo = $kriteriaRepo;
        $this->alternatifRepo = $alternatifRepo;
        $this->penilaianRepo = $penilaianRepo;
        $this->historyRepo = $historyRepo;
        $this->activityLogRepo = $activityLogRepo;
        $this->smartService = $smartService;
    }

    public function index()
    {
        $totalSupplier = count($this->alternatifRepo->all());
        $totalKriteria = count($this->kriteriaRepo->all());
        $totalPenilaian = count($this->penilaianRepo->all());

        // Perform calculation dynamically
        $calculation = $this->smartService->calculate();
        $rankings = $calculation['rankings'];

        $supplierTerbaik = '-';
        $skorTerbaik = 0;
        $supplierTerburuk = '-';
        $skorTerburuk = 0;

        if (count($rankings) > 0) {
            $supplierTerbaik = $rankings[0]['nama'];
            $skorTerbaik = $rankings[0]['skor'];
            $supplierTerburuk = $rankings[count($rankings) - 1]['nama'];
            $skorTerburuk = $rankings[count($rankings) - 1]['skor'];
        }

        // Recent history and logs
        $recentHistory = $this->historyRepo->all()->take(5);
        $recentActivity = $this->activityLogRepo->all(5);

        // Value distribution for Pie Chart
        $distHigh = 0;   // skor >= 75
        $distMedium = 0; // 50 <= skor < 75
        $distLow = 0;    // skor < 50
        foreach ($rankings as $rank) {
            if ($rank['skor'] >= 75) {
                $distHigh++;
            } elseif ($rank['skor'] >= 50) {
                $distMedium++;
            } else {
                $distLow++;
            }
        }

        // Radar chart data preparation
        // We will pass the criteria and their scores per supplier
        $radarData = [];
        $kriterias = $calculation['kriterias'];
        $nilaiMatrix = $calculation['nilai_matrix'];

        foreach ($nilaiMatrix as $row) {
            $radarData[$row['id']] = [
                'nama' => $row['nama'],
                'scores' => array_values($row['nilai'])
            ];
        }

        return view('admin.dashboard', compact(
            'totalSupplier',
            'totalKriteria',
            'totalPenilaian',
            'supplierTerbaik',
            'skorTerbaik',
            'supplierTerburuk',
            'skorTerburuk',
            'rankings',
            'recentHistory',
            'recentActivity',
            'distHigh',
            'distMedium',
            'distLow',
            'kriterias',
            'radarData'
        ));
    }

    public function activity()
    {
        $activities = $this->activityLogRepo->all();
        return view('admin.activity.index', compact('activities'));
    }
}
