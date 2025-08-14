<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Cuenta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CuentaTest extends TestCase
{
    use RefreshDatabase;

    private function createPedidoConItem(): array
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => (string)\Illuminate\Support\Str::uuid(),
            'nombre' => 'Item',
            'cantidad' => 2,
            'precio_unit' => 10,
            'impuesto_porcentaje' => 0,
            'estado' => 'pendiente',
            'estacion' => 'cocina',
            'orden_sec' => 1,
        ]);
        return [$pedido, $item];
    }

    public function test_create_cuenta(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $response = $this->postJson('/api/v1/cuentas', [
            'pedido_id' => $pedido->id,
            'nombre' => 'Cuenta 1'
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('cuentas', 1);
    }

    public function test_create_cuenta_validation_error(): void
    {
        $response = $this->postJson('/api/v1/cuentas', []);
        $response->assertStatus(422);
    }

    public function test_list_cuentas(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta 1']);
        $response = $this->getJson('/api/v1/cuentas');
        $response->assertOk();
    }

    public function test_show_cuenta(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta 1']);
        $response = $this->getJson('/api/v1/cuentas/' . $cuenta->id);
        $response->assertOk();
    }

    public function test_update_cuenta(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta 1']);
        $response = $this->putJson('/api/v1/cuentas/' . $cuenta->id, [
            'nombre' => 'Cuenta X',
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('cuentas', ['id' => $cuenta->id, 'nombre' => 'Cuenta X']);
    }

    public function test_delete_cuenta(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta 1']);
        $response = $this->deleteJson('/api/v1/cuentas/' . $cuenta->id);
        $response->assertOk();
        $this->assertSoftDeleted('cuentas', ['id' => $cuenta->id]);
    }

    public function test_asignar_item_por_cantidad(): void
    {
        [$pedido, $item] = $this->createPedidoConItem();
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta A']);
        $response = $this->postJson('/api/v1/cuentas/' . $cuenta->id . '/items', [
            'items' => [
                ['pedido_item_id' => $item->id, 'cantidad' => 1]
            ]
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('cuenta_items', ['cuenta_id' => $cuenta->id, 'pedido_item_id' => $item->id]);
    }

    public function test_asignar_item_remanente_insuficiente(): void
    {
        [$pedido, $item] = $this->createPedidoConItem();
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta A']);
        $this->postJson('/api/v1/cuentas/' . $cuenta->id . '/items', [
            'items' => [
                ['pedido_item_id' => $item->id, 'cantidad' => 2]
            ]
        ]);
        $response = $this->postJson('/api/v1/cuentas/' . $cuenta->id . '/items', [
            'items' => [
                ['pedido_item_id' => $item->id, 'cantidad' => 1]
            ]
        ]);
        $response->assertStatus(422);
    }

    public function test_cerrar_cuenta_sin_items_error(): void
    {
        $pedido = Pedido::create(['origen' => 'salon']);
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta A']);
        $response = $this->putJson('/api/v1/cuentas/' . $cuenta->id, [
            'nombre' => 'Cuenta A',
            'estado' => 'cerrada'
        ]);
        $response->assertStatus(422);
    }

    public function test_cerrar_cuenta_ok(): void
    {
        [$pedido, $item] = $this->createPedidoConItem();
        $cuenta = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta A']);
        $this->postJson('/api/v1/cuentas/' . $cuenta->id . '/items', [
            'items' => [
                ['pedido_item_id' => $item->id, 'cantidad' => 1]
            ]
        ]);
        $response = $this->putJson('/api/v1/cuentas/' . $cuenta->id, [
            'nombre' => 'Cuenta A',
            'estado' => 'cerrada'
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('cuentas', ['id' => $cuenta->id, 'estado' => 'cerrada']);
    }
}
