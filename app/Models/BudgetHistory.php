<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetHistory extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'budget_amount',
        'selected_menu_id',
        'recommendation_data',
    ];

    /**
     * Disable updated_at timestamp (append-only log)
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget_amount' => 'decimal:2',
            'recommendation_data' => 'array',
        ];
    }

    /**
     * Get the user that created this budget history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu that was selected in this budget history.
     */
    public function selectedMenu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'selected_menu_id');
    }

    /**
     * Save budget history with recommendation data.
     *
     * @param int $userId The ID of the user
     * @param float $budgetAmount The budget amount
     * @param array $recommendationData The recommendation data containing SAW calculation results
     * @param int|null $selectedMenuId The ID of the selected menu (optional)
     * @return self
     * @throws \InvalidArgumentException If recommendation data structure is invalid
     */
    public static function saveRecommendation(
        int $userId,
        float $budgetAmount,
        array $recommendationData,
        ?int $selectedMenuId = null
    ): self {
        // Validate recommendation data structure
        self::validateRecommendationData($recommendationData);

        return self::create([
            'user_id' => $userId,
            'budget_amount' => $budgetAmount,
            'selected_menu_id' => $selectedMenuId,
            'recommendation_data' => $recommendationData,
        ]);
    }

    /**
     * Validate that recommendation data has the required structure.
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException If structure is invalid
     */
    private static function validateRecommendationData(array $data): void
    {
        $requiredKeys = ['criteria_weights', 'ranked_menus', 'calculation_method'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException(
                    "Recommendation data must contain '{$key}' key"
                );
            }
        }

        // Validate that ranked_menus is an array
        if (!is_array($data['ranked_menus'])) {
            throw new \InvalidArgumentException(
                "Recommendation data 'ranked_menus' must be an array"
            );
        }

        // Validate that criteria_weights is an array
        if (!is_array($data['criteria_weights'])) {
            throw new \InvalidArgumentException(
                "Recommendation data 'criteria_weights' must be an array"
            );
        }
    }
}
