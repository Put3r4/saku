<?php

use App\Models\Criterion;
use App\Models\Menu;
use App\Models\MenuEvaluation;
use App\Services\SAWCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new SAWCalculationService();
});

describe('SAW Calculation Service - Core Mathematical Operations', function () {
    describe('normalizeMatrix - Benefit Criteria', function () {
        it('produces maximum value of 1.0 for highest score in benefit criteria', function () {
            // Arrange: Benefit criteria where higher is better
            $matrix = [
                [20, 450], // Menu 1
                [15, 380], // Menu 2
                [10, 500], // Menu 3 (highest kalori)
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Protein', 'tipe' => 'benefit', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Kalori', 'tipe' => 'benefit', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: Highest value in each column should be 1.0
            expect($normalized[0][0])->toBe(1.0); // 20/20 = 1.0 (max protein)
            expect($normalized[2][1])->toBe(1.0); // 500/500 = 1.0 (max kalori)

            // Assert: Other values should be proportional
            expect($normalized[1][0])->toBe(0.75); // 15/20 = 0.75
            expect($normalized[2][0])->toBe(0.5);  // 10/20 = 0.5
            expect($normalized[0][1])->toBe(0.9);  // 450/500 = 0.9
            expect($normalized[1][1])->toBe(0.76); // 380/500 = 0.76
        });

        it('handles all zeros in benefit column without division by zero', function () {
            // Arrange: Column with all zeros
            $matrix = [
                [0, 10],
                [0, 20],
                [0, 30],
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Kriteria A', 'tipe' => 'benefit', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Kriteria B', 'tipe' => 'benefit', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: All values in first column should be 0.0 (no error)
            expect($normalized[0][0])->toBe(0.0);
            expect($normalized[1][0])->toBe(0.0);
            expect($normalized[2][0])->toBe(0.0);

            // Assert: Second column normalizes correctly
            expect($normalized[2][1])->toBe(1.0); // 30/30 = 1.0
        });
    });

    describe('normalizeMatrix - Cost Criteria', function () {
        it('produces maximum value of 1.0 for lowest score in cost criteria', function () {
            // Arrange: Cost criteria where lower is better (e.g., distance)
            $matrix = [
                [1.5, 10], // Menu 1
                [0.8, 5],  // Menu 2 (lowest distance & time) ⭐
                [2.0, 15], // Menu 3
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Jarak (km)', 'tipe' => 'cost', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Waktu (min)', 'tipe' => 'cost', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: Lowest value (best) should get 1.0
            expect($normalized[1][0])->toBe(1.0); // min(0.8) / 0.8 = 1.0
            expect($normalized[1][1])->toBe(1.0); // min(5) / 5 = 1.0

            // Assert: Other values inversely proportional
            expect($normalized[0][0])->toBe(0.5333); // 0.8/1.5 = 0.5333
            expect($normalized[2][0])->toBe(0.4);    // 0.8/2.0 = 0.4
            expect($normalized[0][1])->toBe(0.5);    // 5/10 = 0.5
            expect($normalized[2][1])->toBe(0.3333); // 5/15 = 0.3333
        });

        it('handles all zeros in cost column without division by zero', function () {
            // Arrange: Cost column with all zeros
            $matrix = [
                [0, 10],
                [0, 20],
                [0, 30],
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Jarak', 'tipe' => 'cost', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Waktu', 'tipe' => 'cost', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: All zeros should remain 0.0 (no error)
            expect($normalized[0][0])->toBe(0.0);
            expect($normalized[1][0])->toBe(0.0);
            expect($normalized[2][0])->toBe(0.0);
        });
    });

    describe('calculateWeightedScore - SAW Algorithm Core', function () {
        it('calculates correct weighted scores using example from SAW_ENGINE_ANALYSIS.md', function () {
            // Arrange: Exact example from documentation
            // 3 menus, 4 criteria
            // Weights: [0.4, 0.3, 0.2, 0.1] (Protein 40%, Kalori 30%, Jarak 20%, Porsi 10%)
            // Expected results:
            //   V1 = 0.86 (Menu Ayam)
            //   V2 = 0.81 (Menu Soto)
            //   V3 = 0.68 (Menu Nasi)

            $normalizedMatrix = [
                [1.00, 0.90, 0.53, 0.75], // Menu A1 (Ayam)
                [0.75, 0.76, 1.00, 0.75], // Menu A2 (Soto)
                [0.50, 1.00, 0.40, 1.00], // Menu A3 (Nasi)
            ];

            $weights = [0.4, 0.3, 0.2, 0.1];

            // Act
            $scores = $this->service->calculateWeightedScore($normalizedMatrix, $weights);

            // Assert with tolerance for rounding
            expect(abs($scores[0] - 0.86))->toBeLessThan(0.015);
            expect(abs($scores[1] - 0.81))->toBeLessThan(0.015);
            expect(abs($scores[2] - 0.68))->toBeLessThan(0.015);
        });

        it('handles zero weights correctly', function () {
            // Arrange: Some criteria with zero weight (disabled)
            $normalizedMatrix = [
                [1.00, 0.80],
                [0.50, 1.00],
            ];

            $weights = [1.0, 0.0]; // Only first criterion matters

            // Act
            $scores = $this->service->calculateWeightedScore($normalizedMatrix, $weights);

            // Assert
            expect($scores[0])->toBe(1.0);  // 1.0 * 1.0 + 0.8 * 0.0 = 1.0
            expect($scores[1])->toBe(0.5);  // 0.5 * 1.0 + 1.0 * 0.0 = 0.5
        });
    });

    describe('rankAlternatives - Sorting and Ranking', function () {
        it('ranks alternatives from highest to lowest score correctly', function () {
            // Arrange
            $scores = [0.86, 0.81, 0.68]; // Pre-calculated scores

            $menus = collect([
                Menu::factory()->make(['id' => 1, 'nama' => 'Ayam Goreng']),
                Menu::factory()->make(['id' => 2, 'nama' => 'Soto Ayam']),
                Menu::factory()->make(['id' => 3, 'nama' => 'Nasi Goreng']),
            ]);

            // Act
            $ranked = $this->service->rankAlternatives($scores, $menus);

            // Assert: Should be sorted descending by score
            expect($ranked[0]['rank'])->toBe(1);
            expect($ranked[0]['score'])->toBe(0.86);
            expect($ranked[0]['menu']->nama)->toBe('Ayam Goreng');

            expect($ranked[1]['rank'])->toBe(2);
            expect($ranked[1]['score'])->toBe(0.81);
            expect($ranked[1]['menu']->nama)->toBe('Soto Ayam');

            expect($ranked[2]['rank'])->toBe(3);
            expect($ranked[2]['score'])->toBe(0.68);
            expect($ranked[2]['menu']->nama)->toBe('Nasi Goreng');
        });

        it('handles tied scores correctly', function () {
            // Arrange: Two menus with same score
            $scores = [0.85, 0.85, 0.70];

            $menus = collect([
                Menu::factory()->make(['id' => 1, 'nama' => 'Menu A']),
                Menu::factory()->make(['id' => 2, 'nama' => 'Menu B']),
                Menu::factory()->make(['id' => 3, 'nama' => 'Menu C']),
            ]);

            // Act
            $ranked = $this->service->rankAlternatives($scores, $menus);

            // Assert: Both tied menus should maintain their order
            expect($ranked[0]['score'])->toBe(0.85);
            expect($ranked[1]['score'])->toBe(0.85);
            expect($ranked[2]['score'])->toBe(0.70);
            expect($ranked[2]['rank'])->toBe(3);
        });
    });

    describe('filterMenusByBudget - Hard Constraint', function () {
        it('returns empty collection when budget is very low', function () {
            // Arrange: Create menus with prices above budget
            Menu::factory()->create(['price' => 15000, 'is_available' => true]);
            Menu::factory()->create(['price' => 20000, 'is_available' => true]);
            Menu::factory()->create(['price' => 25000, 'is_available' => true]);

            // Act: Budget too low
            $filtered = $this->service->filterMenusByBudget(5000);

            // Assert
            expect($filtered)->toBeEmpty();
        });

        it('filters menus correctly within budget', function () {
            // Arrange
            Menu::factory()->create(['nama' => 'Murah', 'price' => 10000, 'is_available' => true]);
            Menu::factory()->create(['nama' => 'Sedang', 'price' => 15000, 'is_available' => true]);
            Menu::factory()->create(['nama' => 'Mahal', 'price' => 25000, 'is_available' => true]);

            // Act
            $filtered = $this->service->filterMenusByBudget(16000);

            // Assert: Only 2 menus within budget
            expect($filtered)->toHaveCount(2);
            expect($filtered->pluck('nama')->toArray())->toContain('Murah', 'Sedang');
            expect($filtered->pluck('nama')->toArray())->not->toContain('Mahal');
        });

        it('excludes unavailable menus even if within budget', function () {
            // Arrange
            Menu::factory()->create(['price' => 10000, 'is_available' => true]);
            Menu::factory()->create(['price' => 12000, 'is_available' => false]); // Not available

            // Act
            $filtered = $this->service->filterMenusByBudget(20000);

            // Assert: Only available menu returned
            expect($filtered)->toHaveCount(1);
        });
    });

    describe('getRecommendations - Full SAW Pipeline', function () {
        it('returns empty collection when no menus within budget without error', function () {
            // Arrange: Setup criteria
            Criterion::factory()->create(['tipe' => 'benefit', 'bobot' => 0.5]);
            Criterion::factory()->create(['tipe' => 'benefit', 'bobot' => 0.5]);

            // Create expensive menus
            Menu::factory()->create(['price' => 50000, 'is_available' => true]);

            // Act: Budget too low
            $recommendations = $this->service->getRecommendations(10000);

            // Assert: Should return empty, NOT throw error
            expect($recommendations)->toBeEmpty();
        });

        it('throws exception when criteria are not configured', function () {
            // Arrange: Create menus but NO criteria
            Menu::factory()->create(['price' => 15000, 'is_available' => true]);

            // Act & Assert
            expect(fn () => $this->service->getRecommendations(20000))
                ->toThrow(\Exception::class, 'Kriteria belum dikonfigurasi');
        });

        it('successfully generates recommendations for valid scenario', function () {
            // Arrange: Full setup with criteria, menus, and evaluations
            $criterion1 = Criterion::factory()->create([
                'nama' => 'Protein',
                'tipe' => 'benefit',
                'bobot' => 0.6,
            ]);

            $criterion2 = Criterion::factory()->create([
                'nama' => 'Jarak',
                'tipe' => 'cost',
                'bobot' => 0.4,
            ]);

            $menu1 = Menu::factory()->create(['nama' => 'Ayam', 'price' => 15000, 'is_available' => true]);
            $menu2 = Menu::factory()->create(['nama' => 'Soto', 'price' => 12000, 'is_available' => true]);

            // Evaluations: Menu1 has better protein, Menu2 has better distance
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $criterion1->id, 'value' => 20]); // High protein
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $criterion2->id, 'value' => 2.0]); // Far

            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $criterion1->id, 'value' => 10]); // Low protein
            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $criterion2->id, 'value' => 0.5]); // Near

            // Act
            $recommendations = $this->service->getRecommendations(20000);

            // Assert
            expect($recommendations)->toHaveCount(2);
            expect($recommendations->first()['rank'])->toBe(1);
            expect($recommendations->first()['score'])->toBeGreaterThan(0);

            // Assert ranking is in descending order
            expect($recommendations->first()['score'])->toBeGreaterThanOrEqual(
                $recommendations->last()['score']
            );
        });
    });

    describe('Edge Cases - All Values Same in Column', function () {
        it('handles benefit column with all identical values without division by zero', function () {
            // Arrange: All menus have same protein value
            $matrix = [
                [10, 450],
                [10, 380],
                [10, 500],
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Protein', 'tipe' => 'benefit', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Kalori', 'tipe' => 'benefit', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: All should normalize to 1.0 (since they're all max)
            expect($normalized[0][0])->toBe(1.0); // 10/10 = 1.0
            expect($normalized[1][0])->toBe(1.0); // 10/10 = 1.0
            expect($normalized[2][0])->toBe(1.0); // 10/10 = 1.0
        });

        it('handles cost column with all identical values without division by zero', function () {
            // Arrange: All menus have same distance
            $matrix = [
                [1.5, 10],
                [1.5, 20],
                [1.5, 30],
            ];

            $criteria = collect([
                ['id' => 1, 'nama' => 'Jarak', 'tipe' => 'cost', 'bobot' => 0.5],
                ['id' => 2, 'nama' => 'Waktu', 'tipe' => 'cost', 'bobot' => 0.5],
            ]);

            // Act
            $normalized = $this->service->normalizeMatrix($matrix, $criteria);

            // Assert: All should normalize to 1.0 (since they're all min)
            expect($normalized[0][0])->toBe(1.0); // 1.5/1.5 = 1.0
            expect($normalized[1][0])->toBe(1.0); // 1.5/1.5 = 1.0
            expect($normalized[2][0])->toBe(1.0); // 1.5/1.5 = 1.0
        });
    });
});
