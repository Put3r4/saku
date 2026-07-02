<?php

use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;

test('menu evaluation has correct fillable fields', function () {
    $menuEvaluation = new MenuEvaluation();
    
    expect($menuEvaluation->getFillable())->toBe([
        'menu_id',
        'criterion_id',
        'value',
    ]);
});

test('menu evaluation has timestamps disabled', function () {
    $menuEvaluation = new MenuEvaluation();
    
    expect($menuEvaluation->timestamps)->toBeFalse();
});

test('menu evaluation casts value as decimal:2', function () {
    $menuEvaluation = new MenuEvaluation();
    
    $casts = $menuEvaluation->getCasts();
    
    expect($casts)->toHaveKey('value')
        ->and($casts['value'])->toBe('decimal:2');
});

test('menu evaluation has menu relationship method', function () {
    $menuEvaluation = new MenuEvaluation();
    
    expect(method_exists($menuEvaluation, 'menu'))->toBeTrue();
});

test('menu evaluation has criterion relationship method', function () {
    $menuEvaluation = new MenuEvaluation();
    
    expect(method_exists($menuEvaluation, 'criterion'))->toBeTrue();
});
