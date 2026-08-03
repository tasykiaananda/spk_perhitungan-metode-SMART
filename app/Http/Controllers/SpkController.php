<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    // --- Kriteria ---
    public function getKriteria()
    {
        return response()->json(Kriteria::all());
    }

    public function tambahKriteria(Request $request)
    {
        $request->validate([
            'id' => 'required|string|unique:kriterias,id',
            'nama' => 'required|string',
            'jenis' => 'required|in:Cost,Benefit',
            'rating' => 'required|integer|min:1'
        ]);

        $kriteria = Kriteria::create($request->all());

        return response()->json(['status' => 'success', 'data' => $kriteria]);
    }

    public function updateKriteriaRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1'
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update(['rating' => $request->rating]);

        return response()->json(['status' => 'success', 'data' => $kriteria]);
    }

    public function hapusKriteria($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return response()->json(['status' => 'success']);
    }

    // --- Alternatif ---
    public function getAlternatif()
    {
        return response()->json(Alternatif::all());
    }

    public function tambahAlternatif(Request $request)
    {
        $request->validate([
            'nama' => 'required|string'
        ]);

        $alternatif = Alternatif::create($request->all());

        return response()->json(['status' => 'success', 'data' => $alternatif]);
    }

    public function hapusAlternatif($id)
    {
        $alternatif = Alternatif::findOrFail($id);
        $alternatif->delete();

        return response()->json(['status' => 'success']);
    }

    // --- Penilaian ---
    public function getPenilaian()
    {
        $alternatifs = Alternatif::all();
        $penilaians = Penilaian::all();

        $result = [];
        foreach ($alternatifs as $alt) {
            $row = [
                'alt_id' => $alt->id,
                'nama' => $alt->nama
            ];
            
            // Populate criteria values
            foreach ($penilaians->where('alternatif_id', $alt->id) as $p) {
                $row[$p->kriteria_id] = $p->nilai;
            }
            
            $result[] = $row;
        }

        return response()->json($result);
    }

    public function updatePenilaian(Request $request)
    {
        $request->validate([
            'alternatif_id' => 'required|exists:alternatifs,id',
            'kriteria_id' => 'required|exists:kriterias,id',
            'nilai' => 'required|numeric|min:0'
        ]);

        $penilaian = Penilaian::updateOrCreate(
            [
                'alternatif_id' => $request->alternatif_id,
                'kriteria_id' => $request->kriteria_id
            ],
            ['nilai' => $request->nilai]
        );

        return response()->json(['status' => 'success', 'data' => $penilaian]);
    }
}
