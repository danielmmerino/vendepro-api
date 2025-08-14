<?php

namespace Tests\Feature;

use App\Models\CxcDocumento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturaFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_full_flow(): void
    {
        $resp = $this->postJson('/v1/ventas/facturas', [
            'numero' => 'F-001',
            'fecha' => '2024-01-01',
            'subtotal' => 100,
            'impuesto' => 0,
            'total' => 100,
            'items' => [
                [
                    'producto_id' => '11111111-1111-1111-1111-111111111111',
                    'concepto' => 'Producto',
                    'cantidad' => 1,
                    'precio_unit' => 100,
                    'total_linea' => 100,
                ],
            ],
        ], ['Idempotency-Key'=>'key-1']);
        $resp->assertStatus(201);
        $id = $resp->json('data.id');

        $this->postJson("/v1/ventas/facturas/$id/aprobar")->assertStatus(200)->assertJsonPath('data.estado','aprobada');

        $cxc = CxcDocumento::first();

        $this->postJson('/v1/cxc/pagos',[
            'cxc_id'=>$cxc->id,
            'fecha_pago'=>'2024-01-02',
            'monto'=>50,
        ], ['Idempotency-Key'=>'key-2'])->assertStatus(201);

        $this->postJson('/v1/ventas/notas-credito',[
            'factura_id'=>$id,
            'numero'=>'NC-1',
            'fecha'=>'2024-01-03',
            'motivo'=>'ajuste',
            'subtotal'=>50,
            'impuesto'=>0,
            'total'=>50,
        ], ['Idempotency-Key'=>'key-3'])->assertStatus(201);
        $ncId = \App\Models\NotaCredito::first()->id;
        $this->postJson("/v1/ventas/notas-credito/$ncId/aplicar")->assertStatus(200);

        $this->assertDatabaseHas('cxc_documentos',[
            'id'=>$cxc->id,
            'estado'=>'pagada',
            'saldo_pendiente'=>0,
        ]);
    }

    public function test_delete_aprobada_fails(): void
    {
        $resp = $this->postJson('/v1/ventas/facturas', [
            'numero' => 'F-002',
            'fecha' => '2024-01-01',
            'subtotal' => 10,
            'impuesto' => 0,
            'total' => 10,
            'items' => [[
                'producto_id'=>'11111111-1111-1111-1111-111111111111',
                'concepto'=>'Prod',
                'cantidad'=>1,
                'precio_unit'=>10,
                'total_linea'=>10,
            ]],
        ]);
        $id = $resp->json('data.id');
        $this->postJson("/v1/ventas/facturas/$id/aprobar");
        $this->deleteJson("/v1/ventas/facturas/$id")->assertStatus(422);
    }

    public function test_idempotent_post(): void
    {
        $payload = [
            'numero' => 'F-003',
            'fecha' => '2024-01-01',
            'subtotal' => 10,
            'impuesto' => 0,
            'total' => 10,
            'items' => [[
                'producto_id'=>'11111111-1111-1111-1111-111111111111',
                'concepto'=>'Prod',
                'cantidad'=>1,
                'precio_unit'=>10,
                'total_linea'=>10,
            ]],
        ];
        $this->postJson('/v1/ventas/facturas',$payload,['Idempotency-Key'=>'dup'])->assertStatus(201);
        $this->postJson('/v1/ventas/facturas',$payload,['Idempotency-Key'=>'dup'])->assertStatus(409);
    }
}
