<?php

use App\Models\BudgetHistory;
use App\Models\Criterion;
use App\Models\Menu;
use App\Models\MenuEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Student Recommendation Flow - End-to-End Journey', function () {
    beforeEach(function () {
        // Setup users
        $this->student = User::factory()->create(['role' => 'mahasiswa']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Setup criteria (must sum to 1.0)
        $this->criterion1 = Criterion::factory()->create([
            'kode' => 'C1',
            'nama_kriteria' => 'Protein (g)',
            'tipe' => 'benefit',
            'bobot' => 0.4,
        ]);

        $this->criterion2 = Criterion::factory()->create([
            'kode' => 'C2',
            'nama_kriteria' => 'Kalori (kcal)',
            'tipe' => 'benefit',
            'bobot' => 0.3,
        ]);

        $this->criterion3 = Criterion::factory()->create([
            'kode' => 'C3',
            'nama_kriteria' => 'Jarak (km)',
            'tipe' => 'cost',
            'bobot' => 0.2,
        ]);

        $this->criterion4 = Criterion::factory()->create([
            'kode' => 'C4',
            'nama_kriteria' => 'Porsi',
            'tipe' => 'benefit',
            'bobot' => 0.1,
        ]);
    });

    describe('Complete Student Flow', function () {
        it('completes full journey: login → submit budget → get recommendations → select menu → view history', function () {
            // Step 1: Create menus with various prices
            $menu1 = Menu::factory()->create([
                'vendor_name' => 'Warung Makan Sehat',
                'menu_name' => 'Ayam Goreng + Nasi',
                'price' => 15000,
                'is_available' => true,
            ]);

            $menu2 = Menu::factory()->create([
                'vendor_name' => 'Kantin Kampus',
                'menu_name' => 'Nasi Goreng Telur',
                'price' => 12000,
                'is_available' => true,
            ]);

            $menu3 = Menu::factory()->create([
                'vendor_name' => 'Warteg Pak Budi',
                'menu_name' => 'Nasi + Sayur + Tahu',
                'price' => 10000,
                'is_available' => true,
            ]);

            $menu4 = Menu::factory()->create([
                'vendor_name' => 'Premium Restaurant',
                'menu_name' => 'Steak Daging Sapi',
                'price' => 50000, // Too expensive for budget
                'is_available' => true,
            ]);

            // Create evaluations for menu1 (best overall)
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $this->criterion1->id, 'value' => 25]); // High protein
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $this->criterion2->id, 'value' => 500]); // High kalori
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $this->criterion3->id, 'value' => 1.5]); // Medium distance
            MenuEvaluation::factory()->create(['menu_id' => $menu1->id, 'criterion_id' => $this->criterion4->id, 'value' => 4]); // Large portion

            // Create evaluations for menu2
            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $this->criterion1->id, 'value' => 15]);
            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $this->criterion2->id, 'value' => 400]);
            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $this->criterion3->id, 'value' => 0.5]); // Very near
            MenuEvaluation::factory()->create(['menu_id' => $menu2->id, 'criterion_id' => $this->criterion4->id, 'value' => 3]);

            // Create evaluations for menu3
            MenuEvaluation::factory()->create(['menu_id' => $menu3->id, 'criterion_id' => $this->criterion1->id, 'value' => 10]);
            MenuEvaluation::factory()->create(['menu_id' => $menu3->id, 'criterion_id' => $this->criterion2->id, 'value' => 350]);
            MenuEvaluation::factory()->create(['menu_id' => $menu3->id, 'criterion_id' => $this->criterion3->id, 'value' => 2.0]);
            MenuEvaluation::factory()->create(['menu_id' => $menu3->id, 'criterion_id' => $this->criterion4->id, 'value' => 2]);

            // Create evaluations for expensive menu (should be filtered out)
            MenuEvaluation::factory()->create(['menu_id' => $menu4->id, 'criterion_id' => $this->criterion1->id, 'value' => 50]);
            MenuEvaluation::factory()->create(['menu_id' => $menu4->id, 'criterion_id' => $this->criterion2->id, 'value' => 800]);
            MenuEvaluation::factory()->create(['menu_id' => $menu4->id, 'criterion_id' => $this->criterion3->id, 'value' => 5.0]);
            MenuEvaluation::factory()->create(['menu_id' => $menu4->id, 'criterion_id' => $this->criterion4->id, 'value' => 5]);

            // Step 2: Student logs in and accesses dashboard
            $response = $this->actingAs($this->student)
                ->get(route('student.dashboard'))
                ->assertOk()
                ->assertViewIs('student.dashboard');

            // Step 3: Student submits budget
            $budget = 20000; // Should include menu1, menu2, menu3 but NOT menu4

            $response = $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => $budget])
                ->assertOk()
                ->assertViewIs('student.recommendation')
                ->assertViewHas('budget', $budget)
                ->assertViewHas('recommendations');

            $recommendations = $response->viewData('recommendations');

            // Step 4: Verify recommendations
            // - Should only include menus within budget
            // - Should be ranked by SAW score
            expect($recommendations)->not->toBeEmpty();
            expect($recommendations->count())->toBeLessThanOrEqual(5); // Top 5 only

            // Assert that expensive menu is NOT in recommendations
            $menuIds = $recommendations->pluck('menu_id')->toArray();
            expect($menuIds)->not->toContain($menu4->id);

            // Assert that all recommendations are within budget
            foreach ($recommendations as $rec) {
                expect($rec['price'])->toBeLessThanOrEqual($budget);
            }

            // Assert ranking is correct (descending by score)
            $scores = $recommendations->pluck('saw_score')->toArray();
            $sortedScores = collect($scores)->sortDesc()->values()->toArray();
            expect($scores)->toBe($sortedScores);

            // Step 5: Student selects top recommended menu
            $topRecommendation = $recommendations->first();

            $response = $this->actingAs($this->student)
                ->post(route('student.select-menu'), [
                    'menu_id' => $topRecommendation['menu_id'],
                    'budget' => $budget,
                ])
                ->assertRedirect(route('student.history.index'))
                ->assertSessionHas('success', 'Menu berhasil dipilih dan disimpan ke riwayat!');

            // Step 6: Verify data is saved to budget_histories
            $this->assertDatabaseHas('budget_histories', [
                'user_id' => $this->student->id,
                'budget_amount' => $budget,
                'selected_menu_id' => $topRecommendation['menu_id'],
            ]);

            // Step 7: Verify history page shows the selection
            $response = $this->actingAs($this->student)
                ->get(route('student.history.index'))
                ->assertOk()
                ->assertViewIs('student.history')
                ->assertViewHas('histories');

            $histories = $response->viewData('histories');
            expect($histories)->not->toBeEmpty();
            expect($histories->first()->selected_menu_id)->toBe($topRecommendation['menu_id']);
            expect((float) $histories->first()->budget_amount)->toBe((float) $budget);
        });

        it('handles very low budget gracefully (no menus available)', function () {
            // Create menus with prices above budget
            Menu::factory()->create(['price' => 15000, 'is_available' => true]);
            Menu::factory()->create(['price' => 20000, 'is_available' => true]);

            // Submit very low budget
            $response = $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => 5000])
                ->assertOk()
                ->assertViewIs('student.recommendation')
                ->assertViewHas('recommendations');

            $recommendations = $response->viewData('recommendations');

            // Should return empty collection, not error
            expect($recommendations)->toBeEmpty();
        });

        it('filters out unavailable menus from recommendations', function () {
            // Create available menu
            $availableMenu = Menu::factory()->create(['price' => 10000, 'is_available' => true]);
            MenuEvaluation::factory()->create(['menu_id' => $availableMenu->id, 'criterion_id' => $this->criterion1->id, 'value' => 20]);
            MenuEvaluation::factory()->create(['menu_id' => $availableMenu->id, 'criterion_id' => $this->criterion2->id, 'value' => 400]);
            MenuEvaluation::factory()->create(['menu_id' => $availableMenu->id, 'criterion_id' => $this->criterion3->id, 'value' => 1.0]);
            MenuEvaluation::factory()->create(['menu_id' => $availableMenu->id, 'criterion_id' => $this->criterion4->id, 'value' => 3]);

            // Create unavailable menu
            $unavailableMenu = Menu::factory()->create(['price' => 12000, 'is_available' => false]); // Not available
            MenuEvaluation::factory()->create(['menu_id' => $unavailableMenu->id, 'criterion_id' => $this->criterion1->id, 'value' => 25]);
            MenuEvaluation::factory()->create(['menu_id' => $unavailableMenu->id, 'criterion_id' => $this->criterion2->id, 'value' => 500]);
            MenuEvaluation::factory()->create(['menu_id' => $unavailableMenu->id, 'criterion_id' => $this->criterion3->id, 'value' => 0.5]);
            MenuEvaluation::factory()->create(['menu_id' => $unavailableMenu->id, 'criterion_id' => $this->criterion4->id, 'value' => 4]);

            $response = $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => 20000])
                ->assertOk();

            $recommendations = $response->viewData('recommendations');

            // Should only include available menu
            $menuIds = $recommendations->pluck('menu_id')->toArray();
            expect($menuIds)->toContain($availableMenu->id);
            expect($menuIds)->not->toContain($unavailableMenu->id);
        });
    });

    describe('Budget History Viewing', function () {
        it('shows only current user history, not others', function () {
            $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
            $menu = Menu::factory()->create(['price' => 15000]);

            // Create history for current student
            BudgetHistory::factory()->create([
                'user_id' => $this->student->id,
                'selected_menu_id' => $menu->id,
                'budget_amount' => 20000,
            ]);

            // Create history for other student
            BudgetHistory::factory()->create([
                'user_id' => $otherStudent->id,
                'selected_menu_id' => $menu->id,
                'budget_amount' => 15000,
            ]);

            $response = $this->actingAs($this->student)
                ->get(route('student.history.index'))
                ->assertOk()
                ->assertViewHas('histories');

            $histories = $response->viewData('histories');

            // Should only see own history
            expect($histories)->toHaveCount(1);
            expect($histories->first()->user_id)->toBe($this->student->id);
        });

        it('displays history in reverse chronological order (newest first)', function () {
            $menu = Menu::factory()->create();

            // Create multiple histories at different times
            $old = BudgetHistory::factory()->create([
                'user_id' => $this->student->id,
                'selected_menu_id' => $menu->id,
                'budget_amount' => 10000,
                'created_at' => now()->subDays(2),
            ]);

            $recent = BudgetHistory::factory()->create([
                'user_id' => $this->student->id,
                'selected_menu_id' => $menu->id,
                'budget_amount' => 20000,
                'created_at' => now()->subHours(1),
            ]);

            $response = $this->actingAs($this->student)
                ->get(route('student.history.index'))
                ->assertOk();

            $histories = $response->viewData('histories');

            // Newest should be first
            expect($histories->first()->id)->toBe($recent->id);
            expect($histories->last()->id)->toBe($old->id);
        });

        it('paginates history with 10 items per page', function () {
            $menu = Menu::factory()->create();

            // Create 15 history records
            BudgetHistory::factory()->count(15)->create([
                'user_id' => $this->student->id,
                'selected_menu_id' => $menu->id,
            ]);

            $response = $this->actingAs($this->student)
                ->get(route('student.history.index'))
                ->assertOk();

            $histories = $response->viewData('histories');

            // Should only show 10 on first page
            expect($histories)->toHaveCount(10);

            // Check pagination exists
            expect($histories->hasPages())->toBeTrue();
            expect($histories->total())->toBe(15);
        });
    });

    describe('Budget Constraint Validation', function () {
        it('rejects negative budget', function () {
            $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => -1000])
                ->assertSessionHasErrors(['budget']);
        });

        it('rejects zero budget', function () {
            $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => 0])
                ->assertSessionHasErrors(['budget']);
        });

        it('rejects non-numeric budget', function () {
            $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => 'not-a-number'])
                ->assertSessionHasErrors(['budget']);
        });

        it('accepts valid positive budget', function () {
            Menu::factory()->create(['price' => 10000, 'is_available' => true]);

            $this->actingAs($this->student)
                ->post(route('student.recommend'), ['budget' => 15000])
                ->assertOk()
                ->assertSessionHasNoErrors();
        });
    });

    describe('Menu Selection Validation', function () {
        it('rejects invalid menu_id when selecting', function () {
            $this->actingAs($this->student)
                ->post(route('student.select-menu'), [
                    'menu_id' => 99999, // Non-existent
                    'budget' => 15000,
                ])
                ->assertSessionHasErrors(['menu_id']);
        });

        it('requires budget when selecting menu', function () {
            $menu = Menu::factory()->create();

            $this->actingAs($this->student)
                ->post(route('student.select-menu'), [
                    'menu_id' => $menu->id,
                    // budget missing
                ])
                ->assertSessionHasErrors(['budget']);
        });
    });

    describe('Access Control', function () {
        it('prevents admin from accessing student dashboard', function () {
            $this->actingAs($this->admin)
                ->get(route('student.dashboard'))
                ->assertForbidden();
        });

        it('prevents guest from accessing student features', function () {
            $this->get(route('student.dashboard'))
                ->assertRedirect(route('login'));

            $this->get(route('student.history.index'))
                ->assertRedirect(route('login'));
        });

        it('prevents student from accessing admin features', function () {
            $this->actingAs($this->student)
                ->get(route('admin.criteria.index'))
                ->assertForbidden();

            $this->actingAs($this->student)
                ->get(route('admin.menu.index'))
                ->assertForbidden();
        });
    });
});
