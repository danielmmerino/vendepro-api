<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CxpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_index_with_filters_and_sort(): void
    {
        $prov1 = (string) Str::uuid();
        $prov2 = (string) Str::uuid();
        DB::table('proveedores')->insert([
            ['id' => $prov1, 'empresa_id' => 1, 'identificacion' => '123', 'nombre' => 'Prov Uno', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $prov2, 'empresa_id' => 1, 'identificacion' => '456', 'nombre' => 'Prov Dos', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $comp1 = (string) Str::uuid();
        $comp2 = (string) Str::uuid();
        DB::table('compras')->insert([
            ['id' => $comp1, 'proveedor_id' => $prov1, 'fecha' => '2024-01-10', 'numero_factura' => 'F001', 'subtotal' => 100, 'descuento' => 0, 'impuesto' => 0, 'total' => 100, 'estado' => 'aprobada', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $comp2, 'proveedor_id' => $prov2, 'fecha' => '2024-01-15', 'numero_factura' => 'F002', 'subtotal' => 200, 'descuento' => 0, 'impuesto' => 0, 'total' => 200, 'estado' => 'aprobada', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $cxp1 = (string) Str::uuid();
        $cxp2 = (string) Str::uuid();
        DB::table('cuentas_por_pagar')->insert([
            ['id' => $cxp1, 'compra_id' => $comp1, 'proveedor_id' => $prov1, 'fecha_emision' => '2024-01-10', 'fecha_vencimiento' => '2024-02-10', 'total' => 100, 'saldo_pendiente' => 100, 'estado' => 'pendiente', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $cxp2, 'compra_id' => $comp2, 'proveedor_id' => $prov2, 'fecha_emision' => '2024-01-15', 'fecha_vencimiento' => '2024-02-15', 'total' => 200, 'saldo_pendiente' => 0, 'estado' => 'pagada', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $res = $this->getJson('/v1/cxp?sort=-fecha_emision');
        $res->assertStatus(200)
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonPath('data.0.id', $cxp2);

        $res = $this->getJson("/v1/cxp?proveedor_id=$prov1&estado_saldo=pendiente&fecha_desde=2024-01-01&fecha_hasta=2024-01-31");
        $res->assertStatus(200)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.id', $cxp1);
    }

    public function test_show_returns_detail(): void
    {
        $prov = (string) Str::uuid();
        DB::table('proveedores')->insert([
            'id' => $prov,
            'empresa_id' => 1,
            'identificacion' => '789',
            'nombre' => 'Proveedor X',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $comp = (string) Str::uuid();
        DB::table('compras')->insert([
            'id' => $comp,
            'proveedor_id' => $prov,
            'fecha' => '2024-01-20',
            'numero_factura' => 'F010',
            'subtotal' => 50,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 50,
            'estado' => 'aprobada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cxp = (string) Str::uuid();
        DB::table('cuentas_por_pagar')->insert([
            'id' => $cxp,
            'compra_id' => $comp,
            'proveedor_id' => $prov,
            'fecha_emision' => '2024-01-20',
            'fecha_vencimiento' => '2024-02-20',
            'total' => 50,
            'saldo_pendiente' => 50,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->getJson("/v1/cxp/$cxp");
        $res->assertStatus(200)
            ->assertJsonPath('data.id', $cxp)
            ->assertJsonPath('data.numero_factura', 'F010')
            ->assertJsonPath('data.proveedor', 'Proveedor X');
    }
}
