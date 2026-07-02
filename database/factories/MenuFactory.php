<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'vendor_name' => $this->faker->company(),
            'menu_name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Goreng', 'Bakar', 'Rebus', 'Soto', 'Ayam']),
            'price' => $this->faker->randomFloat(2, 5000, 30000),
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(),
            'is_available' => true,
        ];
    }
}
