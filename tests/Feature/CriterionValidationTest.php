<?php

use App\Models\Criterion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('criterion has validateTotalWeight static method', function () {
    expect(method_exists(Criterion::class, 'validateTotalWeight'))->toBeTrue();
});

test('validateTotalWeight returns true when sum equals 1.00', function () {
    // Create criteria with weights summing to 1.00
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Harga',
        'tipe' => 'cost',
        'bobot' => 0.25,
    ]);
    
    Criterion::create([
        'kode' => 'C2',
        'nama_kriteria' => 'Rasa',
        'tipe' => 'benefit',
        'bobot' => 0.30,
    ]);
    
    Criterion::create([
        'kode' => 'C3',
        'nama_kriteria' => 'Gizi',
        'tipe' => 'benefit',
        'bobot' => 0.45,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeTrue();
});

test('validateTotalWeight returns false when sum is less than 1.00', function () {
    // Create criteria with weights summing to 0.80
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Harga',
        'tipe' => 'cost',
        'bobot' => 0.30,
    ]);
    
    Criterion::create([
        'kode' => 'C2',
        'nama_kriteria' => 'Rasa',
        'tipe' => 'benefit',
        'bobot' => 0.50,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeFalse();
});

test('validateTotalWeight returns false when sum is greater than 1.00', function () {
    // Create criteria with weights summing to 1.20
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Harga',
        'tipe' => 'cost',
        'bobot' => 0.40,
    ]);
    
    Criterion::create([
        'kode' => 'C2',
        'nama_kriteria' => 'Rasa',
        'tipe' => 'benefit',
        'bobot' => 0.80,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeFalse();
});

test('validateTotalWeight handles floating point precision within tolerance', function () {
    // Create criteria with weights that sum to 1.0005 (within ±0.001 tolerance)
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Harga',
        'tipe' => 'cost',
        'bobot' => 0.33335,
    ]);
    
    Criterion::create([
        'kode' => 'C2',
        'nama_kriteria' => 'Rasa',
        'tipe' => 'benefit',
        'bobot' => 0.33335,
    ]);
    
    Criterion::create([
        'kode' => 'C3',
        'nama_kriteria' => 'Gizi',
        'tipe' => 'benefit',
        'bobot' => 0.3333,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeTrue();
});

test('validateTotalWeight returns false for floating point values outside tolerance', function () {
    // Create criteria with weights that sum to 1.002 (outside ±0.001 tolerance)
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Harga',
        'tipe' => 'cost',
        'bobot' => 0.334,
    ]);
    
    Criterion::create([
        'kode' => 'C2',
        'nama_kriteria' => 'Rasa',
        'tipe' => 'benefit',
        'bobot' => 0.334,
    ]);
    
    Criterion::create([
        'kode' => 'C3',
        'nama_kriteria' => 'Gizi',
        'tipe' => 'benefit',
        'bobot' => 0.334,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeFalse();
});

test('validateTotalWeight returns false when no criteria exist', function () {
    // Database is empty due to RefreshDatabase
    $result = Criterion::validateTotalWeight();
    
    // Sum is 0, which is not equal to 1.00
    expect($result)->toBeFalse();
});

test('validateTotalWeight works with single criterion', function () {
    // Create single criterion with weight 1.00
    Criterion::create([
        'kode' => 'C1',
        'nama_kriteria' => 'Overall Score',
        'tipe' => 'benefit',
        'bobot' => 1.00,
    ]);
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeTrue();
});

test('validateTotalWeight with many criteria', function () {
    // Create 5 criteria with equal weights (0.20 each)
    for ($i = 1; $i <= 5; $i++) {
        Criterion::create([
            'kode' => "C{$i}",
            'nama_kriteria' => "Criterion {$i}",
            'tipe' => 'benefit',
            'bobot' => 0.20,
        ]);
    }
    
    $result = Criterion::validateTotalWeight();
    
    expect($result)->toBeTrue();
});
