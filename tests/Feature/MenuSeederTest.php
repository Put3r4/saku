<?php

use App\Models\Criterion;
use App\Models\Menu;
use App\Models\MenuEvaluation;
use Database\Seeders\CriterionSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed criteria first as MenuSeeder depends on it
    $this->seed(CriterionSeeder::class);
    $this->seed(MenuSeeder::class);
});

test('menu seeder creates correct number of menus', function () {
    $menuCount = Menu::count();
    
    expect($menuCount)->toBeGreaterThanOrEqual(15)
        ->and($menuCount)->toBeLessThanOrEqual(20);
});

test('all menus have complete evaluations for all criteria', function () {
    $menus = Menu::with('menuEvaluations')->get();
    $criteriaCount = Criterion::count();
    
    foreach ($menus as $menu) {
        expect($menu->menuEvaluations)->toHaveCount($criteriaCount);
    }
});

test('menu prices are within specified range', function () {
    $menus = Menu::all();
    
    foreach ($menus as $menu) {
        expect($menu->price)->toBeGreaterThanOrEqual(8000)
            ->and($menu->price)->toBeLessThanOrEqual(30000);
    }
});

test('menus include variety of vendor types', function () {
    $menus = Menu::all();
    
    $hasWarung = $menus->contains(fn($menu) => stripos($menu->vendor_name, 'warung') !== false);
    $hasKantin = $menus->contains(fn($menu) => stripos($menu->vendor_name, 'kantin') !== false);
    $hasRestoran = $menus->contains(fn($menu) => stripos($menu->vendor_name, 'restoran') !== false);
    
    expect($hasWarung)->toBeTrue()
        ->and($hasKantin)->toBeTrue()
        ->and($hasRestoran)->toBeTrue();
});

test('menus have realistic indonesian names', function () {
    $menus = Menu::all();
    
    // Check that menus contain common Indonesian food terms
    $indonesianTerms = ['nasi', 'mie', 'soto', 'ayam', 'bakso', 'goreng', 'rendang', 
                        'pecel', 'gado', 'sate', 'rawon', 'uduk', 'liwet'];
    
    $hasIndonesianTerms = false;
    foreach ($menus as $menu) {
        foreach ($indonesianTerms as $term) {
            if (stripos($menu->menu_name, $term) !== false) {
                $hasIndonesianTerms = true;
                break 2;
            }
        }
    }
    
    expect($hasIndonesianTerms)->toBeTrue();
});

test('higher priced menus have better nutrition scores', function () {
    $budgetMenus = Menu::where('price', '<', 15000)->get();
    $premiumMenus = Menu::where('price', '>=', 25000)->get();
    
    $nutritionCriterion = Criterion::where('kode', 'C1')->first();
    
    $avgBudgetNutrition = $budgetMenus->map(function($menu) use ($nutritionCriterion) {
        return $menu->menuEvaluations()
            ->where('criterion_id', $nutritionCriterion->id)
            ->first()->value;
    })->avg();
    
    $avgPremiumNutrition = $premiumMenus->map(function($menu) use ($nutritionCriterion) {
        return $menu->menuEvaluations()
            ->where('criterion_id', $nutritionCriterion->id)
            ->first()->value;
    })->avg();
    
    expect($avgPremiumNutrition)->toBeGreaterThan($avgBudgetNutrition);
});

test('higher priced menus have better hygiene scores', function () {
    $budgetMenus = Menu::where('price', '<', 15000)->get();
    $premiumMenus = Menu::where('price', '>=', 25000)->get();
    
    $hygieneCriterion = Criterion::where('kode', 'C3')->first();
    
    $avgBudgetHygiene = $budgetMenus->map(function($menu) use ($hygieneCriterion) {
        return $menu->menuEvaluations()
            ->where('criterion_id', $hygieneCriterion->id)
            ->first()->value;
    })->avg();
    
    $avgPremiumHygiene = $premiumMenus->map(function($menu) use ($hygieneCriterion) {
        return $menu->menuEvaluations()
            ->where('criterion_id', $hygieneCriterion->id)
            ->first()->value;
    })->avg();
    
    expect($avgPremiumHygiene)->toBeGreaterThan($avgBudgetHygiene);
});

test('distance evaluation values are realistic', function () {
    $distanceCriterion = Criterion::where('kode', 'C2')->first();
    $distances = MenuEvaluation::where('criterion_id', $distanceCriterion->id)->get();
    
    foreach ($distances as $evaluation) {
        // Distance should be between 0.05km (50m) and 2km
        expect($evaluation->value)->toBeGreaterThan(0)
            ->and($evaluation->value)->toBeLessThanOrEqual(2);
    }
});

test('price evaluation matches menu price', function () {
    $priceCriterion = Criterion::where('kode', 'C5')->first();
    $menus = Menu::with('menuEvaluations')->get();
    
    foreach ($menus as $menu) {
        $priceEvaluation = $menu->menuEvaluations()
            ->where('criterion_id', $priceCriterion->id)
            ->first();
        
        expect((float) $priceEvaluation->value)->toBe((float) $menu->price);
    }
});

test('nutrition scores are within valid range', function () {
    $nutritionCriterion = Criterion::where('kode', 'C1')->first();
    $evaluations = MenuEvaluation::where('criterion_id', $nutritionCriterion->id)->get();
    
    foreach ($evaluations as $evaluation) {
        expect($evaluation->value)->toBeGreaterThanOrEqual(1)
            ->and($evaluation->value)->toBeLessThanOrEqual(10);
    }
});

test('hygiene scores are within valid range', function () {
    $hygieneCriterion = Criterion::where('kode', 'C3')->first();
    $evaluations = MenuEvaluation::where('criterion_id', $hygieneCriterion->id)->get();
    
    foreach ($evaluations as $evaluation) {
        expect($evaluation->value)->toBeGreaterThanOrEqual(1)
            ->and($evaluation->value)->toBeLessThanOrEqual(10);
    }
});

test('variety scores are within valid range', function () {
    $varietyCriterion = Criterion::where('kode', 'C4')->first();
    $evaluations = MenuEvaluation::where('criterion_id', $varietyCriterion->id)->get();
    
    foreach ($evaluations as $evaluation) {
        expect($evaluation->value)->toBeGreaterThanOrEqual(1)
            ->and($evaluation->value)->toBeLessThanOrEqual(10);
    }
});

test('all menus are available by default', function () {
    $menus = Menu::all();
    
    foreach ($menus as $menu) {
        expect($menu->is_available)->toBeTrue();
    }
});

test('all menus have descriptions', function () {
    $menus = Menu::all();
    
    foreach ($menus as $menu) {
        expect($menu->description)->not->toBeNull()
            ->and($menu->description)->not->toBe('');
    }
});

test('seeder creates evaluation records with correct relationships', function () {
    $menu = Menu::first();
    $evaluation = $menu->menuEvaluations()->first();
    
    expect($evaluation->menu_id)->toBe($menu->id)
        ->and($evaluation->criterion)->toBeInstanceOf(Criterion::class)
        ->and($evaluation->menu)->toBeInstanceOf(Menu::class);
});
