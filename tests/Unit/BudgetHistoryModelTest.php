<?php

use App\Models\BudgetHistory;

test('budget history has correct fillable fields', function () {
    $budgetHistory = new BudgetHistory();
    
    expect($budgetHistory->getFillable())->toBe([
        'user_id',
        'budget_amount',
        'selected_menu_id',
        'recommendation_data',
    ]);
});

test('budget history has updated_at disabled', function () {
    expect(BudgetHistory::UPDATED_AT)->toBeNull();
});

test('budget history casts budget_amount as decimal:2', function () {
    $budgetHistory = new BudgetHistory();
    
    $casts = $budgetHistory->getCasts();
    
    expect($casts)->toHaveKey('budget_amount')
        ->and($casts['budget_amount'])->toBe('decimal:2');
});

test('budget history casts recommendation_data as array', function () {
    $budgetHistory = new BudgetHistory();
    
    $casts = $budgetHistory->getCasts();
    
    expect($casts)->toHaveKey('recommendation_data')
        ->and($casts['recommendation_data'])->toBe('array');
});

test('budget history has user relationship method', function () {
    $budgetHistory = new BudgetHistory();
    
    expect(method_exists($budgetHistory, 'user'))->toBeTrue();
});

test('budget history has selectedMenu relationship method', function () {
    $budgetHistory = new BudgetHistory();
    
    expect(method_exists($budgetHistory, 'selectedMenu'))->toBeTrue();
});

test('budget history has saveRecommendation static method', function () {
    expect(method_exists(BudgetHistory::class, 'saveRecommendation'))->toBeTrue();
});
