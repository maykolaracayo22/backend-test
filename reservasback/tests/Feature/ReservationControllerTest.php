<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario para autenticación
        $this->user = User::factory()->create();

        // Crear producto para reservas
        $this->product = Product::factory()->create([
            'price' => 100,
        ]);
    }

    /** @test */
    public function it_can_list_all_reservations()
    {
        Reservation::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_show_a_single_reservation()
    {
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $reservation->id,
                     'product_id' => $reservation->product_id,
                     'quantity' => $reservation->quantity,
                 ]);
    }

    /** @test */
    public function it_can_create_a_reservation()
    {
        $data = [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'reservation_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/reservations', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'product_id' => $this->product->id,
                     'quantity' => 2,
                 ]);

        $this->assertDatabaseHas('reservations', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_can_delete_a_reservation()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Reserva eliminada correctamente']);

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    /** @test */
    public function it_prevents_deleting_reservation_of_another_user()
    {
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403)
                 ->assertJson(['message' => 'No autorizado']);

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
    }
}
