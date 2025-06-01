<?php

namespace App\Http\Controllers;

use App\Models\RecordValue;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    const MAX_SCALE = 4;

    public function index(Request $request)
    {
        $reference = $request->query('references');
        if (!$reference) {
            return view('analysis.index', [
                'results' => $results ?? '',
                'reference' => $reference ?? ''
            ]);
        }

        $xData = $this->getVariableData('x', $reference);
        $yData = $this->getVariableData('y', $reference, true);

        $results = [
            'X' => $this->calculateEucsStats($xData),
            'Y' => $this->calculateEucsStats($yData),
            'comparison' => $this->compareDimensions($xData, $yData),
            'achievement' => [
                'X' => $this->calculateAchievement($xData),
                'Y' => $this->calculateAchievement($yData)
            ]
        ];

        return view('analysis.index', [
            'results' => $results,
            'reference' => $reference
        ]);
    }

    private function getVariableData($type, $reference = null, $groupAll = false): Collection
    {
        $query = RecordValue::where('variable', 'like', strtolower($type) . '%');

        if ($reference) {
            $query->where('record_id', $reference);
        }

        return $query->get()->map(function ($item) use ($groupAll) {
            if ($groupAll) {

                $item->main_variable = strtolower(substr($item->variable, 0, 1));
            } else {

                $item->main_variable = preg_replace('/([a-zA-Z]+\d+)\d+/', '$1', $item->variable);
            }
            return $item;
        })->groupBy('main_variable');
    }

    private function calculateEucsStats(Collection $groupedData): array
    {
        $stats = [];

        foreach ($groupedData as $variable => $values) {
            $numericValues = $values->pluck('value')->toArray();
            $n = count($numericValues);

            $sum = array_sum($numericValues);
            $sumSquares = array_sum(array_map(fn($v) => $v ** 2, $numericValues));

            $stats[$variable] = [
                'n' => $n,
                'sum' => $sum,
                'mean' => $n > 0 ? $sum / $n : 0,
                'sum_squares' => $sumSquares,
                'variance' => $n > 1 ? ($sumSquares - ($sum ** 2 / $n)) / ($n - 1) : 0,
                'std_dev' => $n > 1 ? sqrt(($sumSquares - ($sum ** 2 / $n)) / ($n - 1)) : 0,
                'min' => 1,
                'max' => max($numericValues),
                'range' => max($numericValues) - 1
            ];
        }

        return $stats;
    }

    private function calculateAchievement(Collection $groupedData): array
    {
        $achievement = [];

        foreach ($groupedData as $variable => $values) {
            $numericValues = $values->pluck('value')->toArray();
            $n = count($numericValues);
            $sum = array_sum($numericValues);
            $mean = $n > 0 ? $sum / $n : 0;

            $achievementScore = ($mean / self::MAX_SCALE) * 100;

            $achievement[$variable] = [
                'mean' => $mean,
                'achievement_percentage' => $achievementScore,
                'interpretation' => $this->interpretAchievement($achievementScore)
            ];
        }

        return $achievement;
    }

    private function interpretAchievement(float $percentage): string
    {
        if ($percentage >= 90) return 'Sangat Baik';
        if ($percentage >= 80) return 'Baik';            
        if ($percentage >= 70) return 'Cukup';           
        if ($percentage >= 60) return 'Kurang';          
        return 'Sangat Kurang';                         
    }

    private function compareDimensions(Collection $xData, Collection $yData): array
    {
        $comparison = [];
        $allVariables = array_unique(array_merge($xData->keys()->toArray(), $yData->keys()->toArray()));

        foreach ($allVariables as $var) {
            $xValues = $xData->get($var, collect())->pluck('value')->toArray();
            $yValues = $yData->get($var, collect())->pluck('value')->toArray();

            if (!empty($xValues) && !empty($yValues)) {
                $distance = $this->calculateEuclideanDistance($xValues, $yValues);
                $similarity = 1 / (1 + $distance);

                $comparison[$var] = [
                    'distance' => $distance,
                    'similarity' => $similarity,
                    'interpretation' => $this->interpretSimilarity($similarity)
                ];
            }
        }

        return $comparison;
    }

    private function calculateEuclideanDistance(array $x, array $y): float
    {
        $count = min(count($x), count($y));
        $sumSquares = 0;

        for ($i = 0; $i < $count; $i++) {
            $sumSquares += ($x[$i] - $y[$i]) ** 2;
        }

        return sqrt($sumSquares);
    }

    private function interpretSimilarity(float $similarity): string
    {
        if ($similarity >= 0.9) return 'Sangat Mirip';
        if ($similarity >= 0.7) return 'Mirip';
        if ($similarity >= 0.5) return 'Cukup Mirip';
        if ($similarity >= 0.3) return 'Agak Berbeda';
        return 'Sangat Berbeda';
    }
}
