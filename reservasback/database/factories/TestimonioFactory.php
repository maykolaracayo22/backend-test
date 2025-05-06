<?php

namespace Database\Factories;

use App\Models\Testimonio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonioFactory extends Factory
{
    protected $model = Testimonio::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // Creación de usuario asociado
            'nombre'  => $this->faker->name,
            'estrellas' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->sentence,
        ];
    }
}
