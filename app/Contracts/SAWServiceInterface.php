<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SAWServiceInterface
{
    /**
     * Get menu recommendations based on budget using SAW algorithm
     *
     * @param float $budget Maximum budget for menu
     * @return Collection Collection of ranked menu recommendations
     */
    public function getRecommendations(float $budget): Collection;

    /**
     * Filter menus by budget (hard constraint)
     *
     * @param float $budget Maximum budget
     * @return Collection Filtered menus within budget
     */
    public function filterMenusByBudget(float $budget): Collection;

    /**
     * Build decision matrix X from menus and criteria
     *
     * @param Collection $menus Collection of Menu models
     * @param Collection $criteria Collection of Criterion models
     * @return array 2D array [menu_index][criterion_index] = value
     */
    public function buildDecisionMatrix(Collection $menus, Collection $criteria): array;

    /**
     * Normalize decision matrix to R
     * - Benefit criteria: rij = xij / max(xij)
     * - Cost criteria: rij = min(xij) / xij
     *
     * @param array $matrix Decision matrix
     * @param Collection $criteria Collection of Criterion models
     * @return array Normalized matrix
     */
    public function normalizeMatrix(array $matrix, Collection $criteria): array;

    /**
     * Calculate weighted score Vi = Σ(wj × rij)
     *
     * @param array $normalizedMatrix Normalized matrix
     * @param array $weights Array of criterion weights
     * @return array Array of scores for each alternative
     */
    public function calculateWeightedScore(array $normalizedMatrix, array $weights): array;

    /**
     * Rank alternatives by score descending
     *
     * @param array $scores Array of scores
     * @param Collection $menus Collection of Menu models
     * @return Collection Ranked alternatives with score and rank
     */
    public function rankAlternatives(array $scores, Collection $menus): Collection;
}
