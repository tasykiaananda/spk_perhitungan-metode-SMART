<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\KriteriaRepositoryInterface;
use App\Repositories\Contracts\AlternatifRepositoryInterface;
use App\Repositories\Contracts\PenilaianRepositoryInterface;
use App\Models\ActivityLog;

class PenilaianController extends Controller
{
    protected $kriteriaRepo;
    protected $alternatifRepo;
    protected $penilaianRepo;

    public function __construct(
        KriteriaRepositoryInterface $kriteriaRepo,
        AlternatifRepositoryInterface $alternatifRepo,
        PenilaianRepositoryInterface $penilaianRepo
    ) {
        $this->kriteriaRepo = $kriteriaRepo;
        $this->alternatifRepo = $alternatifRepo;
        $this->penilaianRepo = $penilaianRepo;
    }

    public function index()
    {
        $kriterias = $this->kriteriaRepo->all();
        $alternatifs = $this->alternatifRepo->all();
        $penilaians = $this->penilaianRepo->all();

        // Structure a convenient grid array for the Blade view
        $matrix = [];
        foreach ($alternatifs as $alt) {
            $matrix[$alt->id] = [
                'nama' => $alt->nama,
                'scores' => []
            ];
            foreach ($kriterias as $k) {
                $p = $penilaians->where('alternatif_id', $alt->id)->where('kriteria_id', $k->id)->first();
                $matrix[$alt->id]['scores'][$k->id] = $p ? $p->nilai : 0;
            }
        }

        return view('admin.penilaian.index', compact('kriterias', 'alternatifs', 'matrix'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'alternatif_id' => 'required|exists:alternatifs,id',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0',
        ]);

        $altId = $request->alternatif_id;
        $alternatif = $this->alternatifRepo->find($altId);

        foreach ($request->scores as $kId => $val) {
            $this->penilaianRepo->updateOrCreate($altId, $kId, $val);
        }

        ActivityLog::log("Memperbarui nilai penilaian untuk supplier: {$alternatif->nama}");

        return redirect()->route('admin.penilaian.index')->with('success', 'Nilai penilaian berhasil diperbarui!');
    }

    public function report()
    {
        $kriterias = $this->kriteriaRepo->all();
        $alternatifs = $this->alternatifRepo->all();
        $penilaians = $this->penilaianRepo->all();

        $matrix = [];
        foreach ($alternatifs as $alt) {
            $matrix[$alt->id] = [
                'nama' => $alt->nama,
                'scores' => []
            ];
            foreach ($kriterias as $k) {
                $p = $penilaians->where('alternatif_id', $alt->id)->where('kriteria_id', $k->id)->first();
                $matrix[$alt->id]['scores'][$k->id] = $p ? $p->nilai : 0;
            }
        }

        ActivityLog::log("Mencetak laporan matriks penilaian");

        return view('admin.penilaian.report', compact('kriterias', 'alternatifs', 'matrix'));
    }
}
