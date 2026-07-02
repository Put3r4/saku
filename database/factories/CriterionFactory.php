<?php

namespace Database\Factories;

use App\Models\Criterion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CriterionFactory extends Factory
{
    protected $model = Criterion::class;

    public function definition(): array
    {
        return [
            'kode' => $this->faker->unique()->bothify('C#'),
            'nama_kriteria' => $this->faker->word(),
            'tipe' => $this->faker->randomElement(['benefit', 'cost']),
            'bobot' => $this->faker->randomFloat(2, 0.1, 0.5),
        ];
    }
}
