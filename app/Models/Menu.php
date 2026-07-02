<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_name',
        'menu_name',
        'price',
        'description',
        'image_url',
        'is_available',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the menu evaluations for the menu.
     */
    public function menuEvaluations(): HasMany
    {
        return $this->hasMany(MenuEvaluation::class);
    }

    /**
     * Alias for menuEvaluations (used in SAW calculation)
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(MenuEvaluation::class);
    }

    /**
     * Get the criteria that belong to the menu.
     */
    public function criteria(): BelongsToMany
    {
        return $this->belongsToMany(Criterion::class, 'menu_evaluations')
            ->withPivot('value');
    }

    /**
     * Get the budget histories for the menu.
     */
    public function budgetHistories(): HasMany
    {
        return $this->hasMany(BudgetHistory::class, 'selected_menu_id');
    }

    /**
     * Scope a query to only include available menus.
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_available', true);
    }

    /**
     * Scope a query to only include menus within budget.
     */
    public function scopeWithinBudget(Builder $query, float $budget): void
    {
        $query->where('price', '<=', $budget);
    }

    /**
     * Accessor for 'nama' attribute (mapping to menu_name).
     */
    public function getNamaAttribute(): ?string
    {
        return $this->menu_name;
    }

    /**
     * Mutator for 'nama' attribute (mapping to menu_name).
     */
    public function setNamaAttribute(?string $value): void
    {
        $this->attributes['menu_name'] = $value;
    }

    /**
     * Accessor for 'name' attribute (mapping to menu_name).
     */
    public function getNameAttribute(): ?string
    {
        return $this->menu_name;
    }

    /**
     * Accessor for 'vendor' attribute (mapping to vendor_name).
     */
    public function getVendorAttribute(): ?string
    {
        return $this->vendor_name;
    }
}
