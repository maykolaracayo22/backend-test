<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $quantity = $this->faker->numberBetween(1, 5);
        $statusAllowed = ['pendiente', 'confirmada', 'cancelada', 'completada']; // en español

        return [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'reservation_code' => 'RES-' . $this->faker->unique()->bothify('??###'),
            'quantity' => $quantity,
            'total_amount' => $product->price * $quantity,
            'status' => $this->faker->randomElement($statusAllowed),
            'start_date' => $this->faker->date(),
        ];
    }

}
