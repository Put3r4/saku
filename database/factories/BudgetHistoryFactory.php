<?php

namespace Database\Factories;

use App\Models\BudgetHistory;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetHistoryFactory extends Factory
{
    protected $model = BudgetHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'budget_amount' => $this->faker->randomFloat(2, 10000, 50000),
            'selected_menu_id' => Menu::factory(),
            'recommendation_data' => [
                'criteria_weights' => ['C1' => 0.4, 'C2' => 0.3, 'C3' => 0.2, 'C4' => 0.1],
                'ranked_menus' => [
                    ['menu_id' => 1, 'menu_name' => 'Ayam Goreng', 'price' => 15000, 'saw_score' => 0.85, 'rank' => 1]
                ],
                'calculation_method' => 'SAW',
            ],
            'created_at' => now(),
        ];
    }
}
