<?php

namespace App\Services;

use App\Repositories\Contracts\KriteriaRepositoryInterface;
use App\Repositories\Contracts\AlternatifRepositoryInterface;
use App\Repositories\Contracts\PenilaianRepositoryInterface;

class SmartService
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

    /**
     * Perform the full SMART calculation process.
     */
    public function calculate()
    {
        $kriterias = $this->kriteriaRepo->all();
        $alternatifs = $this->alternatifRepo->all();
        $penilaians = $this->penilaianRepo->all();

        // 1. Calculate normalized weights
        $totalRating = $kriterias->sum('rating');
        $normalizedKriterias = $kriterias->map(function ($k) use ($totalRating) {
            $k->bobot = $totalRating > 0 ? $k->rating / $totalRating : 0;
            return $k;
        });

        // 2. Map assessments: alternatif -> criteria -> nilai
        $nilaiMatrix = [];
        foreach ($alternatifs as $alt) {
            $nilaiMatrix[$alt->id] = [
                'id' => $alt->id,
                'nama' => $alt->nama,
                'nilai' => []
            ];
            foreach ($kriterias as $k) {
                $p = $penilaians->where('alternatif_id', $alt->id)->where('kriteria_id', $k->id)->first();
                $nilaiMatrix[$alt->id]['nilai'][$k->id] = $p ? (double)$p->nilai : 0.0;
            }
        }

        // 3. Find min/max values per criteria
        $minMax = [];
        foreach ($kriterias as $k) {
            $values = [];
            foreach ($alternatifs as $alt) {
                $values[] = $nilaiMatrix[$alt->id]['nilai'][$k->id] ?? 0.0;
            }
            $minMax[$k->id] = [
                'min' => count($values) > 0 ? min($values) : 0.0,
                'max' => count($values) > 0 ? max($values) : 0.0
            ];
        }

        // 4. Calculate Utility (u) and Weighted Utility (W * u)
        $utilityMatrix = [];
        $weightedMatrix = [];
        $finalScores = [];

        foreach ($alternatifs as $alt) {
            $utilityMatrix[$alt->id] = [
                'id' => $alt->id,
                'nama' => $alt->nama,
                'values' => []
            ];
            $weightedMatrix[$alt->id] = [
                'id' => $alt->id,
                'nama' => $alt->nama,
                'values' => [],
                'total' => 0.0
            ];

            $totalScore = 0.0;
            foreach ($normalizedKriterias as $k) {
                $val = $nilaiMatrix[$alt->id]['nilai'][$k->id] ?? 0.0;
                $cMin = $minMax[$k->id]['min'];
                $cMax = $minMax[$k->id]['max'];
                $w = $k->bobot;

                // Utility formula
                $u = 0.0;
                if ($cMax !== $cMin) {
                    if ($k->jenis === 'Cost') {
                        $u = (($cMax - $val) / ($cMax - $cMin)) * 100;
                    } else {
                        // Benefit
                        $u = (($val - $cMin) / ($cMax - $cMin)) * 100;
                    }
                } else {
                    $u = 100.0; // If all alternatives have same value, utility is max (100)
                }

                $utilityMatrix[$alt->id]['values'][$k->id] = $u;
                
                $weightedVal = $w * $u;
                $weightedMatrix[$alt->id]['values'][$k->id] = $weightedVal;
                $totalScore += $weightedVal;
            }

            $weightedMatrix[$alt->id]['total'] = $totalScore;
            $finalScores[] = [
                'id' => $alt->id,
                'nama' => $alt->nama,
                'skor' => $totalScore
            ];
        }

        // 5. Rank the results
        usort($finalScores, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Add ranking numbers
        $rankedList = [];
        foreach ($finalScores as $index => $item) {
            $item['ranking'] = $index + 1;
            $rankedList[] = $item;
        }

        return [
            'kriterias' => $normalizedKriterias,
            'total_rating' => $totalRating,
            'nilai_matrix' => array_values($nilaiMatrix),
            'min_max' => $minMax,
            'utility_matrix' => array_values($utilityMatrix),
            'weighted_matrix' => array_values($weightedMatrix),
            'rankings' => $rankedList
        ];
    }

    /**
     * Get detail calculation steps for a specific supplier.
     */
    public function getDetail($alternatifId)
    {
        $kriterias = $this->kriteriaRepo->all();
        $alternatif = $this->alternatifRepo->find($alternatifId);
        $penilaians = $this->penilaianRepo->all();

        // 1. Total rating and normalization
        $totalRating = $kriterias->sum('rating');
        $kriteriasWithWeights = $kriterias->map(function ($k) use ($totalRating) {
            $k->bobot = $totalRating > 0 ? $k->rating / $totalRating : 0;
            return $k;
        });

        // 2. Fetch assessor values for all alternatives to calculate min/max
        $allAlternatifs = $this->alternatifRepo->all();
        $nilaiMatrix = [];
        foreach ($allAlternatifs as $alt) {
            foreach ($kriterias as $k) {
                $p = $penilaians->where('alternatif_id', $alt->id)->where('kriteria_id', $k->id)->first();
                $nilaiMatrix[$k->id][] = $p ? (double)$p->nilai : 0.0;
            }
        }

        $minMax = [];
        foreach ($kriterias as $k) {
            $vals = $nilaiMatrix[$k->id] ?? [0.0];
            $minMax[$k->id] = [
                'min' => min($vals),
                'max' => max($vals)
            ];
        }

        // 3. Get results for target supplier
        $steps = [];
        $totalScore = 0.0;

        foreach ($kriteriasWithWeights as $k) {
            $p = $penilaians->where('alternatif_id', $alternatif->id)->where('kriteria_id', $k->id)->first();
            $val = $p ? (double)$p->nilai : 0.0;
            $cMin = $minMax[$k->id]['min'];
            $cMax = $minMax[$k->id]['max'];
            $w = $k->bobot;

            $u = 0.0;
            $formula = '';
            $substitusi = '';

            if ($cMax !== $cMin) {
                if ($k->jenis === 'Cost') {
                    $u = (($cMax - $val) / ($cMax - $cMin)) * 100;
                    $formula = 'u(a) = ((C_max - C) / (C_max - C_min)) * 100';
                    $substitusi = "(( $cMax - $val ) / ( $cMax - $cMin )) * 100 = " . round($u, 4);
                } else {
                    $u = (($val - $cMin) / ($cMax - $cMin)) * 100;
                    $formula = 'u(a) = ((C - C_min) / (C_max - C_min)) * 100';
                    $substitusi = "(( $val - $cMin ) / ( $cMax - $cMin )) * 100 = " . round($u, 4);
                }
            } else {
                $u = 100.0;
                $formula = 'u(a) = 100 (C_max = C_min)';
                $substitusi = "100.00";
            }

            $weightedVal = $w * $u;
            $totalScore += $weightedVal;

            $steps[] = [
                'kriteria_id' => $k->id,
                'kriteria_nama' => $k->nama,
                'jenis' => $k->jenis,
                'rating' => $k->rating,
                'bobot' => $w,
                'nilai_asli' => $val,
                'min' => $cMin,
                'max' => $cMax,
                'formula' => $formula,
                'substitusi' => $substitusi,
                'utility' => $u,
                'weighted' => $weightedVal
            ];
        }

        // Determine current ranking
        $allCalculated = $this->calculate();
        $rankData = collect($allCalculated['rankings'])->where('id', $alternatifId)->first();
        $rank = $rankData ? $rankData['ranking'] : null;

        return [
            'alternatif' => $alternatif,
            'steps' => $steps,
            'total_skor' => $totalScore,
            'ranking' => $rank
        ];
    }
}
