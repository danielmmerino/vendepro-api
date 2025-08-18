<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotaCreditoAliasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_alias_routes(): void
    {
        $payload = [
            'factura_id' => '11111111-1111-1111-1111-111111111111',
            'numero' => 'NC-ALIAS',
            'fecha' => '2024-01-01',
            'motivo' => 'ajuste',
            'subtotal' => 10,
            'impuesto' => 0,
            'total' => 10,
        ];
        $this->postJson('/v1/notas-credito', $payload, ['Idempotency-Key'=>'alias-1'])->assertStatus(201);
        $id = \App\Models\NotaCredito::first()->id;
        $this->getJson('/v1/notas-credito')->assertStatus(200);
        $this->getJson("/v1/ventas/notas-credito/$id")->assertStatus(200);
    }
}
