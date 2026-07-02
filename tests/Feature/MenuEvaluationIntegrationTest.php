<?php

use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create menu evaluation with all required fields', function () {
    // Create a criterion
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    // Create a menu
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    // Create menu evaluation
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    expect($menuEvaluation->exists)->toBeTrue()
        ->and($menuEvaluation->menu_id)->toBe($menu->id)
        ->and($menuEvaluation->criterion_id)->toBe($criterion->id)
        ->and((string) $menuEvaluation->value)->toBe('85.50');
});

test('menu evaluation casts value to decimal with 2 decimal places', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.5,
    ]);
    
    // Refresh to get the data from database
    $menuEvaluation->refresh();
    
    expect((string) $menuEvaluation->value)->toBe('85.50');
});

test('menu evaluation belongs to menu relationship works', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    $relatedMenu = $menuEvaluation->menu;
    
    expect($relatedMenu)->toBeInstanceOf(Menu::class)
        ->and($relatedMenu->id)->toBe($menu->id)
        ->and($relatedMenu->menu_name)->toBe('Test Menu');
});

test('menu evaluation belongs to criterion relationship works', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    $relatedCriterion = $menuEvaluation->criterion;
    
    expect($relatedCriterion)->toBeInstanceOf(Criterion::class)
        ->and($relatedCriterion->id)->toBe($criterion->id)
        ->and($relatedCriterion->nama_kriteria)->toBe('Test Criterion');
});

test('menu evaluation enforces unique constraint on menu_id and criterion_id', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    // Create first evaluation
    MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    // Try to create duplicate evaluation - should throw exception
    expect(fn() => MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 90.00,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('menu evaluation cascades delete when menu is deleted', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    $evaluationId = $menuEvaluation->id;
    
    // Delete the menu
    $menu->delete();
    
    // Menu evaluation should also be deleted
    expect(MenuEvaluation::find($evaluationId))->toBeNull();
});

test('menu evaluation cascades delete when criterion is deleted', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    $evaluationId = $menuEvaluation->id;
    
    // Delete the criterion
    $criterion->delete();
    
    // Menu evaluation should also be deleted
    expect(MenuEvaluation::find($evaluationId))->toBeNull();
});

test('menu evaluation does not have timestamps', function () {
    $criterion = Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Test Criterion',
        'tipe' => 'benefit',
        'bobot' => 0.25,
    ]);
    
    $menu = Menu::create([
        'vendor_name' => 'Test Vendor',
        'menu_name' => 'Test Menu',
        'price' => 15000.00,
        'is_available' => true,
    ]);
    
    $menuEvaluation = MenuEvaluation::create([
        'menu_id' => $menu->id,
        'criterion_id' => $criterion->id,
        'value' => 85.50,
    ]);
    
    expect($menuEvaluation->created_at)->toBeNull()
        ->and($menuEvaluation->updated_at)->toBeNull();
});
