<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'entrepreneur_id' => User::factory(),  // Crea usuario emprendedor
            'place_id' => null,
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(50, 500),
            'stock' => $this->faker->numberBetween(1, 100),
            'duration' => $this->faker->numberBetween(1, 10),
            'main_image' => null,
            'is_active' => true,
        ];
    }
}
