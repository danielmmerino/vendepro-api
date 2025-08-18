<?php

namespace Tests\Feature\Sri;

use App\Models\Sri\{Emisor, Establecimiento, PuntoEmision, Secuencia};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecuenciaNextTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    private function baseData(): array
    {
        $emisor = Emisor::create([
            'id' => Str::uuid(),
            'ruc' => '0123456789001',
            'razon_social' => 'Demo',
            'direccion_matriz' => 'Quito',
        ]);

        $estab = Establecimiento::create([
            'id' => Str::uuid(),
            'emisor_id' => $emisor->id,
            'codigo' => '001',
            'direccion' => 'Dir',
        ]);

        $punto = PuntoEmision::create([
            'id' => Str::uuid(),
            'establecimiento_id' => $estab->id,
            'codigo' => '001',
            'sec_hasta' => 999999999,
        ]);

        $secuencia = Secuencia::create([
            'id' => Str::uuid(),
            'punto_emision_id' => $punto->id,
            'tipo' => '01',
            'actual' => 1,
        ]);

        return [$emisor, $estab, $punto, $secuencia];
    }

    public function test_next_secuencial(): void
    {
        [$emisor] = $this->baseData();

        $response = $this->postJson('/api/v1/sri/secuencias/next', [
            'emisor_id' => $emisor->id,
            'establecimiento' => '001',
            'punto' => '001',
            'tipo' => '01',
        ]);

        $response->assertOk()->assertJson(['data' => ['secuencial' => 2]]);
    }

    public function test_next_secuencial_not_found(): void
    {
        [$emisor] = $this->baseData();

        $response = $this->postJson('/api/v1/sri/secuencias/next', [
            'emisor_id' => $emisor->id,
            'establecimiento' => '001',
            'punto' => '001',
            'tipo' => '04',
        ]);

        $response->assertStatus(500);
    }
}
