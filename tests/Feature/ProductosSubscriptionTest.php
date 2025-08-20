<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductosSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_user_without_local_can_access_productos_using_empresa_id(): void
    {
        $empresaId = DB::table('empresas')->where('ruc', '1790012345001')->value('id');

        $userId = DB::table('usuarios')->insertGetId([
            'empresa_id' => $empresaId,
            'nombre' => 'Empresa Admin',
            'email' => 'empresaadmin@vendepro.io',
            'password_hash' => Hash::make('VendePro#2025'),
            'activo' => 1,
            'token_version' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::find($userId);
        $token = app(JwtService::class)->generate($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/productos?empresa_id=' . $empresaId);

        $response->assertStatus(200);
    }
}
