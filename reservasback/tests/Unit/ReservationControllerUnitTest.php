<?php

namespace Tests\Unit;

use Mockery;
use App\Http\Controllers\ReservationController;
use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReservationControllerUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock usuario autenticado
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn((object) ['id' => 1]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function index_returns_all_reservations_with_mock()
    {
        $fakeReservations = collect([
            (object)['id' => 1, 'product_id' => 1, 'quantity' => 2],
            (object)['id' => 2, 'product_id' => 2, 'quantity' => 1],
        ]);

        $reservationMock = Mockery::mock(Reservation::class);
        $reservationMock->shouldReceive('with')->once()->with(['product', 'user'])->andReturnSelf();
        $reservationMock->shouldReceive('get')->once()->andReturn($fakeReservations);

        $productMock = Mockery::mock(Product::class);

        $controller = new ReservationController($reservationMock, $productMock);

        $response = $controller->index();

        $this->assertEquals(200, $response->status());
        $this->assertEquals($fakeReservations->toJson(), $response->getContent());
    }

    /** @test */
    public function show_returns_the_requested_reservation_with_mock()
    {
        $fakeReservation = (object)['id' => 42, 'product_id' => 5, 'quantity' => 3];

        $reservationMock = Mockery::mock(Reservation::class);
        $reservationMock->shouldReceive('with')->once()->with(['product.entrepreneur', 'user'])->andReturnSelf();
        $reservationMock->shouldReceive('findOrFail')->once()->with(42)->andReturn($fakeReservation);

        $productMock = Mockery::mock(Product::class);

        $controller = new ReservationController($reservationMock, $productMock);

        $response = $controller->show(42);

        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode($fakeReservation), $response->getContent());
    }

/** @test */
    public function store_validates_and_creates_reservation_with_mock()
    {
        $requestData = [
            'product_id' => 3,
            'quantity' => 4,
            'reservation_date' => '2025-06-01',
        ];

        // Mock parcial de Request para saltar validación
        $requestMock = Mockery::mock(Request::class)->makePartial();
        $requestMock->shouldReceive('validate')->once()->andReturn(true);
        $requestMock->shouldReceive('all')->andReturn($requestData);

        // Mock producto con precio
        $fakeProduct = (object) ['id' => 3, 'price' => 50];

        $productMock = Mockery::mock(Product::class);
        $productMock->shouldReceive('findOrFail')->once()->with($requestData['product_id'])->andReturn($fakeProduct);

        // Mock parcial reserva con método load
        $fakeReservation = Mockery::mock(Reservation::class)->makePartial();
        $fakeReservation->shouldReceive('load')->once()->with('product.entrepreneur')->andReturnSelf();
        $fakeReservation->id = 99;
        $fakeReservation->product_id = 3;
        $fakeReservation->quantity = 4;
        $fakeReservation->reservation_date = '2025-06-01';

        $reservationMock = Mockery::mock(Reservation::class);
        $reservationMock->shouldReceive('create')->once()->andReturn($fakeReservation);

        $controller = new ReservationController($reservationMock, $productMock);

        $response = $controller->store($requestMock);

        $this->assertEquals(201, $response->status());
        $this->assertEquals(json_encode($fakeReservation), $response->getContent());
    }


    /** @test */
    public function destroy_deletes_reservation_and_returns_message_with_mock()
    {
        // Mock parcial para simular modelo con user_id y método delete
        $fakeReservationMock = Mockery::mock(Reservation::class)->makePartial();
        $fakeReservationMock->user_id = 1;
        $fakeReservationMock->shouldReceive('delete')->once()->andReturn(true);

        $reservationMock = Mockery::mock(Reservation::class);
        $reservationMock->shouldReceive('find')->once()->with(7)->andReturn($fakeReservationMock);

        $productMock = Mockery::mock(Product::class);

        $controller = new ReservationController($reservationMock, $productMock);

        $response = $controller->destroy(7);

        $this->assertEquals(200, $response->status());
        $this->assertJson($response->getContent());
    }
}
