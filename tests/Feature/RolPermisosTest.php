<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RolPermisosTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $email, string $password): string
    {
        $response = $this->postJson('/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);
        $response->assertStatus(200);
        return $response->json('token');
    }

    public function test_requires_authentication(): void
    {
        $rolId = DB::table('roles')->where('codigo', 'CAJERO')->value('id');
        $this->postJson("/v1/roles/{$rolId}/permisos", ['permisos' => []])
            ->assertStatus(401);
    }

    public function test_forbidden_without_permission(): void
    {
        $rolId = DB::table('roles')->where('codigo', 'CAJERO')->value('id');
        $token = $this->login('adminlocal@vendepro.io', 'VendePro#2025');
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/v1/roles/{$rolId}/permisos", ['permisos' => []])
            ->assertStatus(403);
    }

    public function test_assign_permissions_success(): void
    {
        $rolId = DB::table('roles')->where('codigo', 'CAJERO')->value('id');
        $token = $this->login('superadmin@vendepro.io', 'VendePro#2025');
        $permCode = 'usuarios.ver';
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/v1/roles/{$rolId}/permisos", ['permisos' => [$permCode]])
            ->assertStatus(200)
            ->assertJsonPath('data.permisos.0', $permCode);
        $permId = DB::table('permisos')->where('codigo', $permCode)->value('id');
        $this->assertDatabaseHas('rol_permisos', ['rol_id' => $rolId, 'permiso_id' => $permId]);
    }
}

