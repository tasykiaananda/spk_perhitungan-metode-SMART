<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\KriteriaRepositoryInterface;
use App\Models\ActivityLog;

class KriteriaController extends Controller
{
    protected $kriteriaRepo;

    public function __construct(KriteriaRepositoryInterface $kriteriaRepo)
    {
        $this->kriteriaRepo = $kriteriaRepo;
    }

    public function index()
    {
        $kriterias = $this->kriteriaRepo->all();
        
        // Calculate normalized weights in real-time
        $totalRating = $kriterias->sum('rating');
        $kriterias = $kriterias->map(function ($k) use ($totalRating) {
            $k->bobot = $totalRating > 0 ? $k->rating / $totalRating : 0;
            return $k;
        });

        return view('admin.kriteria.index', compact('kriterias', 'totalRating'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:10|unique:kriterias,id',
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Cost,Benefit',
            'rating' => 'required|integer|min:1|max:100',
        ], [
            'id.unique' => 'Kode Kriteria sudah digunakan.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 100.',
        ]);

        $kriteria = $this->kriteriaRepo->create($request->all());

        ActivityLog::log("Menambahkan kriteria baru: {$kriteria->id} - {$kriteria->nama}");

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Cost,Benefit',
            'rating' => 'required|integer|min:1|max:100',
        ]);

        $kriteria = $this->kriteriaRepo->update($id, $request->only(['nama', 'jenis', 'rating']));

        ActivityLog::log("Mengubah kriteria: {$kriteria->id} ({$kriteria->nama})");

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kriteria = $this->kriteriaRepo->find($id);
        $this->kriteriaRepo->delete($id);

        ActivityLog::log("Menghapus kriteria: {$id} - {$kriteria->nama}");

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}
