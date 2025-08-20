<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_can_create_mesa_with_local_id(): void
    {
        $response = $this->postJson('/v1/mesas', [
            'local_id' => 1,
            'codigo' => 'M1',
            'nombre' => 'Mesa 1',
            'capacidad' => 4,
            'ubicacion' => 'salon',
            'estado' => 'activa',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.local_id', 1)
            ->assertJsonPath('data.codigo', 'M1');

        $this->assertDatabaseHas('mesas', ['codigo' => 'M1', 'local_id' => 1]);
    }

    public function test_requires_local_id_returns_422(): void
    {
        $response = $this->postJson('/v1/mesas', [
            'codigo' => 'M2',
            'nombre' => 'Mesa 2',
            'capacidad' => 4,
            'ubicacion' => 'salon',
            'estado' => 'activa',
        ]);

        $response->assertStatus(422);
    }
}
