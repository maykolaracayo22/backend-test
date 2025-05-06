<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\Testimonio;
use App\Models\User;
use App\Http\Controllers\Api\TestimonioController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestimonioControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_all_testimonios()
    {
        // Crea un usuario de prueba
        $user = User::factory()->create(['name' => 'Juan']);
        
        // Crea un testimonio de prueba
        Testimonio::factory()->create([
            'user_id'   => $user->id,
            'estrellas' => 5,
            'comentario'=> 'Excelente experiencia',
        ]);

        // Instancia el controlador y llama al método 'index'
        $controller = new TestimonioController();
        $response = $controller->index();

        // Verifica que la respuesta tenga código de estado 200
        $this->assertEquals(200, $response->status());

        // Verifica que la respuesta tenga la cantidad de testimonios esperada
        $content = $response->getData(true);
        $this->assertCount(1, $content);

        // Verifica los valores del primer testimonio
        $this->assertEquals('Juan', $content[0]['nombre']);
        $this->assertEquals('Excelente experiencia', $content[0]['comentario']);
    }

    /** @test */
    public function store_validates_and_creates_testimonio_with_user()
    {
        // Crea un usuario de prueba
        $user = User::factory()->create(['name' => 'Ana']);

        // Crea la petición para almacenar un testimonio
        $request = Request::create('/api/testimonios', 'POST', [
            'user_id'   => $user->id,
            'estrellas' => 4,
            'comentario'=> 'Muy bueno',
        ]);

        // Instancia el controlador y llama al método 'store'
        $controller = new TestimonioController();
        $response = $controller->store($request);

        // Verifica que la respuesta tenga código de estado 201
        $this->assertEquals(201, $response->status());

        // Verifica los valores en la respuesta
        $content = $response->getData(true);
        $this->assertEquals('Ana', $content['nombre']);
        $this->assertEquals(4, $content['estrellas']);
    }

    /** @test */
    public function store_validates_and_creates_testimonio_without_user()
    {
        // Crea la petición para almacenar un testimonio sin usuario
        $request = Request::create('/api/testimonios', 'POST', [
            'nombre'    => 'Carlos',
            'estrellas' => 5,
            'comentario'=> 'Perfecto',
        ]);

        // Instancia el controlador y llama al método 'store'
        $controller = new TestimonioController();
        $response = $controller->store($request);

        // Verifica que la respuesta tenga código de estado 201
        $this->assertEquals(201, $response->status());

        // Verifica los valores en la respuesta
        $content = $response->getData(true);
        $this->assertEquals('Carlos', $content['nombre']);
        $this->assertEquals('Perfecto', $content['comentario']);
    }
}
