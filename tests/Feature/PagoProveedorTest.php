<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PagoProveedorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_store_creates_payment_and_updates_cxp(): void
    {
        $prov = (string) Str::uuid();
        DB::table('proveedores')->insert([
            'id' => $prov,
            'empresa_id' => 1,
            'identificacion' => '123',
            'nombre' => 'Prov',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $compra = (string) Str::uuid();
        DB::table('compras')->insert([
            'id' => $compra,
            'proveedor_id' => $prov,
            'fecha' => '2024-01-01',
            'numero_factura' => 'F1',
            'subtotal' => 100,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 100,
            'estado' => 'aprobada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cxp = (string) Str::uuid();
        DB::table('cuentas_por_pagar')->insert([
            'id' => $cxp,
            'compra_id' => $compra,
            'proveedor_id' => $prov,
            'fecha_emision' => '2024-01-01',
            'fecha_vencimiento' => '2024-02-01',
            'total' => 100,
            'saldo_pendiente' => 100,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->postJson('/v1/pagos-proveedor', [
            'cxp_id' => $cxp,
            'fecha_pago' => '2024-01-10',
            'monto' => 60,
            'forma_pago' => 'efectivo',
            'referencia' => 'ref123',
        ]);
        $res->assertStatus(201)
            ->assertJsonPath('data.cxp_id', $cxp);

        $saldo = DB::table('cuentas_por_pagar')->where('id', $cxp)->value('saldo_pendiente');
        $estado = DB::table('cuentas_por_pagar')->where('id', $cxp)->value('estado');
        $this->assertEquals(40.0, (float) $saldo);
        $this->assertEquals('pendiente', $estado);
    }

    public function test_store_fails_when_monto_exceeds_saldo(): void
    {
        $prov = (string) Str::uuid();
        DB::table('proveedores')->insert([
            'id' => $prov,
            'empresa_id' => 1,
            'identificacion' => '456',
            'nombre' => 'Prov2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $compra = (string) Str::uuid();
        DB::table('compras')->insert([
            'id' => $compra,
            'proveedor_id' => $prov,
            'fecha' => '2024-01-05',
            'numero_factura' => 'F2',
            'subtotal' => 80,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 80,
            'estado' => 'aprobada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cxp = (string) Str::uuid();
        DB::table('cuentas_por_pagar')->insert([
            'id' => $cxp,
            'compra_id' => $compra,
            'proveedor_id' => $prov,
            'fecha_emision' => '2024-01-05',
            'fecha_vencimiento' => '2024-02-05',
            'total' => 80,
            'saldo_pendiente' => 80,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->postJson('/v1/pagos-proveedor', [
            'cxp_id' => $cxp,
            'fecha_pago' => '2024-01-15',
            'monto' => 100,
            'forma_pago' => 'transferencia',
        ]);
        $res->assertStatus(409);
    }
}
