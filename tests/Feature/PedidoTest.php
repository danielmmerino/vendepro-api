<?php

namespace Tests\Feature;

use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_pedido(): void
    {
        $response = $this->postJson('/api/v1/pedidos', [
            'origen' => 'salon'
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount('pedidos', 1);
    }

    public function test_create_pedido_validation_error(): void
    {
        $response = $this->postJson('/api/v1/pedidos', []);
        $response->assertStatus(422);
    }
}
