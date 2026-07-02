<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterion extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode', 
        'nama_kriteria', 
        'tipe', 
        'bobot'
    ];

    /**
     * Get all menu evaluations for this criterion.
     */
    public function menuEvaluations(): HasMany
    {
        return $this->hasMany(MenuEvaluation::class);
    }

    /**
     * Get all menus that have been evaluated against this criterion.
     * Includes the evaluation value as a pivot.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_evaluations')
            ->withPivot('value');
    }

    /**
     * Validate that the sum of all criterion weights equals 1.00.
     * 
     * Checks if the total of all 'bobot' values from all Criterion records
     * equals 1.00 with a tolerance of ±0.001 to handle floating-point precision.
     * 
     * @return bool True if the sum is valid (1.00 ±0.001), false otherwise
     */
    public static function validateTotalWeight(): bool
    {
        $totalWeight = self::sum('bobot');
        return abs($totalWeight - 1.00) < 0.001;
    }

    /**
     * Accessor for 'nama' attribute (mapping to nama_kriteria).
     */
    public function getNamaAttribute(): ?string
    {
        return $this->nama_kriteria;
    }

    /**
     * Mutator for 'nama' attribute (mapping to nama_kriteria).
     */
    public function setNamaAttribute(?string $value): void
    {
        $this->attributes['nama_kriteria'] = $value;
    }
}