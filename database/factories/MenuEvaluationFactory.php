<?php

namespace Database\Factories;

use App\Models\MenuEvaluation;
use App\Models\Menu;
use App\Models\Criterion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuEvaluationFactory extends Factory
{
    protected $model = MenuEvaluation::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'criterion_id' => Criterion::factory(),
            'value' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
