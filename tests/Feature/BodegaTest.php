<?php

namespace Tests\Feature;

use App\Models\Bodega;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BodegaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_can_create_bodega(): void
    {
        $response = $this->postJson('/v1/bodegas', [
            'local_id' => 1,
            'nombre' => 'Matriz',
            'es_principal' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.nombre', 'Matriz');
        $this->assertDatabaseHas('bodegas', ['nombre' => 'Matriz']);
    }

    public function test_requires_nombre_returns_422(): void
    {
        $response = $this->postJson('/v1/bodegas', [
            'local_id' => 1,
            'es_principal' => true,
        ]);

        $response->assertStatus(422);
    }

    public function test_index_with_filters(): void
    {
        Bodega::factory()->create(['nombre' => 'Matriz', 'local_id' => 1, 'es_principal' => true]);
        Bodega::factory()->create(['nombre' => 'Secundaria', 'local_id' => 2, 'es_principal' => false]);

        $response = $this->getJson('/v1/bodegas?q=matriz&sort=-nombre');
        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.nombre', 'Matriz');
    }

    public function test_can_update_bodega(): void
    {
        $bodega = Bodega::factory()->create(['nombre' => 'Matriz']);

        $response = $this->putJson("/v1/bodegas/{$bodega->id}", [
            'local_id' => 1,
            'nombre' => 'Central',
            'es_principal' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.nombre', 'Central');
    }

    public function test_delete_blocked_when_has_saldo(): void
    {
        $bodega = Bodega::factory()->create();
        DB::table('inventario_saldos')->insert([
            'bodega_id' => $bodega->id,
            'producto_id' => 1,
            'cantidad' => 5,
            'costo_promedio' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson("/v1/bodegas/{$bodega->id}");
        $response->assertStatus(409);
    }
}
