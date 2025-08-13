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
            'codigo' => 'BOD-001',
            'nombre' => 'Matriz',
            'estado' => 'activa',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.codigo', 'BOD-001');
        $this->assertDatabaseHas('bodegas', ['codigo' => 'BOD-001']);
    }

    public function test_duplicate_codigo_returns_422(): void
    {
        Bodega::factory()->create(['codigo' => 'BOD-001']);

        $response = $this->postJson('/v1/bodegas', [
            'codigo' => 'BOD-001',
            'nombre' => 'Otra',
            'estado' => 'activa',
        ]);

        $response->assertStatus(422);
    }

    public function test_index_with_filters(): void
    {
        Bodega::factory()->create(['codigo' => 'BOD-001', 'nombre' => 'Matriz', 'estado' => 'activa']);
        Bodega::factory()->create(['codigo' => 'BOD-002', 'nombre' => 'Secundaria', 'estado' => 'inactiva']);

        $response = $this->getJson('/v1/bodegas?q=matriz&estado=activa&sort=-nombre');
        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.codigo', 'BOD-001');
    }

    public function test_can_update_bodega(): void
    {
        $bodega = Bodega::factory()->create(['codigo' => 'BOD-001', 'nombre' => 'Matriz']);

        $response = $this->putJson("/v1/bodegas/{$bodega->id}", [
            'codigo' => 'BOD-001',
            'nombre' => 'Central',
            'estado' => 'activa',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.nombre', 'Central');
    }

    public function test_delete_blocked_when_has_saldo(): void
    {
        $bodega = Bodega::factory()->create(['codigo' => 'BOD-001']);
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
