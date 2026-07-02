<?php

use App\Models\Criterion;
use App\Models\MenuEvaluation;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('criterion has correct fillable fields', function () {
    $criterion = new Criterion();
    
    expect($criterion->getFillable())->toBe([
        'kode',
        'nama_kriteria',
        'tipe',
        'bobot',
    ]);
});

test('criterion has menuEvaluations relationship method', function () {
    $criterion = new Criterion();
    
    expect(method_exists($criterion, 'menuEvaluations'))->toBeTrue();
});

test('criterion menuEvaluations returns HasMany relationship', function () {
    $criterion = new Criterion();
    
    $relationship = $criterion->menuEvaluations();
    
    expect($relationship)->toBeInstanceOf(HasMany::class);
});

test('criterion has menus relationship method', function () {
    $criterion = new Criterion();
    
    expect(method_exists($criterion, 'menus'))->toBeTrue();
});

test('criterion menus returns BelongsToMany relationship', function () {
    $criterion = new Criterion();
    
    $relationship = $criterion->menus();
    
    expect($relationship)->toBeInstanceOf(BelongsToMany::class);
});

test('criterion menus relationship includes value pivot', function () {
    $criterion = new Criterion();
    
    $relationship = $criterion->menus();
    
    // Check that 'value' is included in the pivot columns
    expect($relationship->getPivotColumns())->toContain('value');
});
