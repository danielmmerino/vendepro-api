<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Cuenta;
use App\Models\CuentaItem;

class CuentaSeeder extends Seeder
{
    public function run(): void
    {
        $pedido = Pedido::create([
            'origen' => 'salon',
            'estado' => 'abierto',
            'subtotal' => 0,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 0,
        ]);
        $item1 = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => (string)\Illuminate\Support\Str::uuid(),
            'nombre' => 'Item 1',
            'cantidad' => 2,
            'precio_unit' => 10,
            'impuesto_porcentaje' => 0,
            'estado' => 'pendiente',
            'estacion' => 'cocina',
            'orden_sec' => 1,
        ]);
        $item2 = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => (string)\Illuminate\Support\Str::uuid(),
            'nombre' => 'Item 2',
            'cantidad' => 1,
            'precio_unit' => 20,
            'impuesto_porcentaje' => 0,
            'estado' => 'pendiente',
            'estacion' => 'cocina',
            'orden_sec' => 2,
        ]);
        $cuenta1 = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta A']);
        $cuenta2 = Cuenta::create(['pedido_id' => $pedido->id, 'nombre' => 'Cuenta B']);
        CuentaItem::create([
            'cuenta_id' => $cuenta1->id,
            'pedido_item_id' => $item1->id,
            'cantidad' => 1,
            'monto' => 10,
            'impuesto_monto' => 0,
        ]);
        CuentaItem::create([
            'cuenta_id' => $cuenta2->id,
            'pedido_item_id' => $item2->id,
            'cantidad' => 1,
            'monto' => 20,
            'impuesto_monto' => 0,
        ]);
    }
}
