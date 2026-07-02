<?php

use App\Models\User;
use App\Models\Menu;
use App\Models\BudgetHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('saveRecommendation creates budget history with all required fields', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);

    $recommendationData = [
        'criteria_weights' => [
            'C1' => 0.25,
            'C2' => 0.20,
        ],
        'ranked_menus' => [
            [
                'menu_id' => $menu->id,
                'menu_name' => 'Test Menu',
                'vendor_name' => 'Test Vendor',
                'price' => 15000,
                'total_score' => 0.85,
                'normalized_values' => [
                    'C1' => 0.90,
                    'C2' => 0.80,
                ],
            ],
        ],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 20000.00,
        recommendationData: $recommendationData,
        selectedMenuId: $menu->id
    );

    expect($budgetHistory->exists)->toBeTrue()
        ->and($budgetHistory->user_id)->toBe($user->id)
        ->and((string) $budgetHistory->budget_amount)->toBe('20000.00')
        ->and($budgetHistory->selected_menu_id)->toBe($menu->id)
        ->and($budgetHistory->recommendation_data)->toBe($recommendationData);
});

test('saveRecommendation works without selected menu', function () {
    $user = User::factory()->create();

    $recommendationData = [
        'criteria_weights' => [
            'C1' => 0.30,
        ],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData
    );

    expect($budgetHistory->exists)->toBeTrue()
        ->and($budgetHistory->user_id)->toBe($user->id)
        ->and((string) $budgetHistory->budget_amount)->toBe('15000.00')
        ->and($budgetHistory->selected_menu_id)->toBeNull()
        ->and($budgetHistory->recommendation_data)->toBe($recommendationData);
});

test('saveRecommendation validates criteria_weights key exists', function () {
    $user = User::factory()->create();

    $invalidData = [
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    expect(fn() => BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $invalidData
    ))->toThrow(InvalidArgumentException::class, "Recommendation data must contain 'criteria_weights' key");
});

test('saveRecommendation validates ranked_menus key exists', function () {
    $user = User::factory()->create();

    $invalidData = [
        'criteria_weights' => ['C1' => 0.25],
        'calculation_method' => 'SAW',
    ];

    expect(fn() => BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $invalidData
    ))->toThrow(InvalidArgumentException::class, "Recommendation data must contain 'ranked_menus' key");
});

test('saveRecommendation validates calculation_method key exists', function () {
    $user = User::factory()->create();

    $invalidData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
    ];

    expect(fn() => BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $invalidData
    ))->toThrow(InvalidArgumentException::class, "Recommendation data must contain 'calculation_method' key");
});

test('saveRecommendation validates criteria_weights is an array', function () {
    $user = User::factory()->create();

    $invalidData = [
        'criteria_weights' => 'not an array',
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    expect(fn() => BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $invalidData
    ))->toThrow(InvalidArgumentException::class, "Recommendation data 'criteria_weights' must be an array");
});

test('saveRecommendation validates ranked_menus is an array', function () {
    $user = User::factory()->create();

    $invalidData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => 'not an array',
        'calculation_method' => 'SAW',
    ];

    expect(fn() => BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $invalidData
    ))->toThrow(InvalidArgumentException::class, "Recommendation data 'ranked_menus' must be an array");
});

test('budget history only has created_at timestamp', function () {
    $user = User::factory()->create();

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData
    );

    expect($budgetHistory->created_at)->not->toBeNull()
        ->and($budgetHistory->updated_at)->toBeNull();
});

test('budget history belongs to user relationship works', function () {
    $user = User::factory()->create(['name' => 'Test User']);

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData
    );

    $relatedUser = $budgetHistory->user;

    expect($relatedUser)->toBeInstanceOf(User::class)
        ->and($relatedUser->id)->toBe($user->id)
        ->and($relatedUser->name)->toBe('Test User');
});

test('budget history belongs to selected menu relationship works', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'vendor_name' => 'Warung Bu Tini',
        'menu_name' => 'Nasi Goreng',
        'price' => 12000.00,
        'is_available' => true,
    ]);

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [
            [
                'menu_id' => $menu->id,
                'menu_name' => 'Nasi Goreng',
                'total_score' => 0.85,
            ],
        ],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData,
        selectedMenuId: $menu->id
    );

    $relatedMenu = $budgetHistory->selectedMenu;

    expect($relatedMenu)->toBeInstanceOf(Menu::class)
        ->and($relatedMenu->id)->toBe($menu->id)
        ->and($relatedMenu->menu_name)->toBe('Nasi Goreng');
});

test('budget history preserves when selected menu is deleted', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [
            [
                'menu_id' => $menu->id,
                'menu_name' => 'Test Menu',
                'total_score' => 0.85,
            ],
        ],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData,
        selectedMenuId: $menu->id
    );

    $historyId = $budgetHistory->id;

    // Delete the menu
    $menu->delete();

    // Budget history should still exist with selected_menu_id set to null
    $history = BudgetHistory::find($historyId);
    expect($history)->not->toBeNull()
        ->and($history->selected_menu_id)->toBeNull()
        ->and($history->recommendation_data)->toBe($recommendationData); // Data preserved
});

test('budget history cascades delete when user is deleted', function () {
    $user = User::factory()->create();

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData
    );

    $historyId = $budgetHistory->id;

    // Delete the user
    $user->delete();

    // Budget history should also be deleted
    expect(BudgetHistory::find($historyId))->toBeNull();
});

test('budget history casts budget_amount to decimal with 2 decimal places', function () {
    $user = User::factory()->create();

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000,
        recommendationData: $recommendationData
    );

    $budgetHistory->refresh();

    expect((string) $budgetHistory->budget_amount)->toBe('15000.00');
});

test('budget history stores and retrieves complex recommendation data correctly', function () {
    $user = User::factory()->create();

    $complexRecommendationData = [
        'criteria_weights' => [
            'C1' => 0.25,
            'C2' => 0.20,
            'C3' => 0.30,
            'C4' => 0.15,
            'C5' => 0.10,
        ],
        'ranked_menus' => [
            [
                'menu_id' => 1,
                'menu_name' => 'Nasi Goreng Spesial',
                'vendor_name' => 'Warung Pak Joko',
                'price' => 15000,
                'total_score' => 0.85,
                'normalized_values' => [
                    'C1' => 0.90,
                    'C2' => 0.80,
                    'C3' => 0.95,
                    'C4' => 0.75,
                    'C5' => 0.85,
                ],
            ],
            [
                'menu_id' => 2,
                'menu_name' => 'Ayam Geprek',
                'vendor_name' => 'Kantin Pusat',
                'price' => 12000,
                'total_score' => 0.78,
                'normalized_values' => [
                    'C1' => 0.85,
                    'C2' => 0.70,
                    'C3' => 0.80,
                    'C4' => 0.90,
                    'C5' => 0.65,
                ],
            ],
        ],
        'calculation_method' => 'SAW',
    ];

    $budgetHistory = BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 20000.00,
        recommendationData: $complexRecommendationData
    );

    $budgetHistory->refresh();

    expect($budgetHistory->recommendation_data)->toBe($complexRecommendationData)
        ->and($budgetHistory->recommendation_data['criteria_weights'])->toHaveCount(5)
        ->and($budgetHistory->recommendation_data['ranked_menus'])->toHaveCount(2)
        ->and($budgetHistory->recommendation_data['calculation_method'])->toBe('SAW');
});

test('user can have multiple budget histories', function () {
    $user = User::factory()->create();

    $recommendationData = [
        'criteria_weights' => ['C1' => 0.25],
        'ranked_menus' => [],
        'calculation_method' => 'SAW',
    ];

    // Create multiple budget histories
    BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 15000.00,
        recommendationData: $recommendationData
    );

    BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 20000.00,
        recommendationData: $recommendationData
    );

    BudgetHistory::saveRecommendation(
        userId: $user->id,
        budgetAmount: 25000.00,
        recommendationData: $recommendationData
    );

    $histories = $user->budgetHistories;

    expect($histories)->toHaveCount(3);
});
