<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\CajaApertura;
use App\Models\PagoVenta;

class CajaPagosVentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_routes_require_auth()
    {
        $this->postJson('/api/v1/caja/aperturas', [])->assertStatus(401);
    }

    public function test_apertura_duplicate_conflict()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        CajaApertura::create([
            'local_id'=>1,'caja_id'=>1,'usuario_id'=>$user->id,'saldo_inicial'=>0
        ]);
        $this->postJson('/api/v1/caja/aperturas',[
            'local_id'=>1,'caja_id'=>1,'usuario_id'=>$user->id,'saldo_inicial'=>0
        ])->assertStatus(409);
    }

    public function test_pago_cash_without_apertura_conflict()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $this->postJson('/api/v1/pagos-venta',[
            'factura_id'=>1,
            'items_pago'=>[['metodo'=>'efectivo','monto'=>10]],
            'caja'=>['apertura_id'=>999]
        ])->assertStatus(409);
    }
}
