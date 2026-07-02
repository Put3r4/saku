<?php

namespace App\Services;

use App\Contracts\SAWServiceInterface;
use App\Models\Criterion;
use App\Models\Menu;
use Illuminate\Support\Collection;

class SAWCalculationService implements SAWServiceInterface
{
    /**
     * Get menu recommendations based on budget using SAW algorithm
     *
     * @param float $budget Maximum budget for menu
     * @return Collection Collection of ranked menu recommendations
     * @throws \Exception When criteria are not configured
     */
    public function getRecommendations(float $budget): Collection
    {
        // Step 1: Filter menus by budget (hard constraint)
        $menus = $this->filterMenusByBudget($budget);

        // Edge case: No menus available within budget
        if ($menus->isEmpty()) {
            return collect([]);
        }

        // Step 2: Get all criteria with weights
        $criteria = Criterion::all();

        // Edge case: Criteria not configured
        if ($criteria->isEmpty()) {
            throw new \Exception('Kriteria belum dikonfigurasi. Silakan tambahkan kriteria terlebih dahulu.');
        }

        // Step 3: Build decision matrix X
        $matrix = $this->buildDecisionMatrix($menus, $criteria);

        // Step 4: Normalize matrix to R
        $normalizedMatrix = $this->normalizeMatrix($matrix, $criteria);

        // Step 5: Calculate weighted scores Vi = Σ(wj × rij)
        $weights = $criteria->pluck('bobot')->toArray();
        $scores = $this->calculateWeightedScore($normalizedMatrix, $weights);

        // Step 6: Rank alternatives by score
        return $this->rankAlternatives($scores, $menus);
    }

    /**
     * Filter menus by budget (hard constraint, not a criterion)
     *
     * @param float $budget Maximum budget
     * @return Collection Filtered menus within budget
     */
    public function filterMenusByBudget(float $budget): Collection
    {
        return Menu::where('price', '<=', $budget)
            ->where('is_available', true)
            ->with('evaluations.criterion')
            ->get();
    }

    /**
     * Build decision matrix X from menus and criteria
     * Matrix berisi nilai rating mentah: xij = nilai menu i pada kriteria j
     *
     * @param Collection $menus Collection of Menu models
     * @param Collection $criteria Collection of Criterion models
     * @return array 2D array [menu_index][criterion_index] = value
     */
    public function buildDecisionMatrix(Collection $menus, Collection $criteria): array
    {
        $matrix = [];

        foreach ($menus as $menu) {
            $row = [];

            foreach ($criteria as $criterion) {
                // Find evaluation value for this menu-criterion pair
                $evaluation = $menu->evaluations
                    ->where('criterion_id', $criterion->id)
                    ->first();

                // Default to 0 if evaluation doesn't exist
                $row[] = $evaluation ? (float) $evaluation->value : 0.0;
            }

            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Normalize decision matrix to R
     * - Benefit criteria: rij = xij / max(xij)
     * - Cost criteria: rij = min(xij) / xij
     * - Handle edge case: all values are 0 (avoid division by zero)
     *
     * @param array $matrix Decision matrix
     * @param Collection $criteria Collection of Criterion models
     * @return array Normalized matrix
     */
    public function normalizeMatrix(array $matrix, Collection $criteria): array
    {
        // Edge case: empty matrix
        if (empty($matrix)) {
            return [];
        }

        $normalized = [];
        $criteriaArray = $criteria->values()->toArray();

        // Transpose matrix to work with columns (easier for normalization)
        $columns = $this->transpose($matrix);

        foreach ($criteriaArray as $j => $criterion) {
            $column = $columns[$j] ?? [];

            if ($criterion['tipe'] === 'benefit') {
                // BENEFIT: rij = xij / max(xij)
                // Makin besar makin baik (protein, kalori, porsi, rating, dll)
                $max = max($column);

                // Edge case: semua nilai 0, hindari division by zero
                if ($max == 0) {
                    $normalized[$j] = array_fill(0, count($column), 0.0);
                } else {
                    $normalized[$j] = array_map(fn ($val) => round($val / $max, 4), $column);
                }
            } else {
                // COST: rij = min(xij) / xij
                // Makin kecil makin baik (jarak, waktu penyajian, dll)

                // Filter out zero values to find actual minimum
                $nonZeroValues = array_filter($column, fn ($val) => $val > 0);

                // Edge case: semua nilai 0 atau tidak ada nilai positif
                if (empty($nonZeroValues)) {
                    $normalized[$j] = array_fill(0, count($column), 0.0);
                } else {
                    $min = min($nonZeroValues);
                    $normalized[$j] = array_map(
                        fn ($val) => $val > 0 ? round($min / $val, 4) : 0.0,
                        $column
                    );
                }
            }
        }

        // Transpose back to rows [menu_index][criterion_index]
        return $this->transpose($normalized);
    }

    /**
     * Calculate weighted score Vi = Σ(wj × rij)
     * Score rounded to 4 decimal places
     *
     * @param array $normalizedMatrix Normalized matrix
     * @param array $weights Array of criterion weights (must sum to 1.0)
     * @return array Array of scores for each alternative
     */
    public function calculateWeightedScore(array $normalizedMatrix, array $weights): array
    {
        $scores = [];

        foreach ($normalizedMatrix as $row) {
            $score = 0.0;

            foreach ($row as $j => $value) {
                $score += $weights[$j] * $value;
            }

            // Round to 4 decimal places
            $scores[] = round($score, 4);
        }

        return $scores;
    }

    /**
     * Rank alternatives by score descending
     * Highest score gets rank 1
     *
     * @param array $scores Array of scores
     * @param Collection $menus Collection of Menu models
     * @return Collection Ranked alternatives with menu, score, and rank
     */
    public function rankAlternatives(array $scores, Collection $menus): Collection
    {
        $ranked = [];

        // Combine menus with their scores
        foreach ($menus as $i => $menu) {
            $ranked[] = [
                'menu' => $menu,
                'score' => $scores[$i],
                'rank' => 0, // Will be assigned after sorting
            ];
        }

        // Sort by score descending (highest score first)
        usort($ranked, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Assign rank (1, 2, 3, ...)
        foreach ($ranked as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        return collect($ranked);
    }

    /**
     * Transpose a 2D matrix.
     *
     * @param array $matrix
     * @return array
     */
    private function transpose(array $matrix): array
    {
        if (empty($matrix) || empty($matrix[0])) {
            return [];
        }

        $transposed = [];
        $numRows = count($matrix);
        $numCols = count($matrix[0]);

        for ($col = 0; $col < $numCols; $col++) {
            $rowValues = [];
            for ($row = 0; $row < $numRows; $row++) {
                $rowValues[] = $matrix[$row][$col];
            }
            $transposed[] = $rowValues;
        }

        return $transposed;
    }
}
