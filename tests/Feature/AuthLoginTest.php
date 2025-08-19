<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_login_returns_token_and_local_id(): void
    {
        $response = $this->postJson('/v1/auth/login', [
            'email' => 'superadmin@vendepro.io',
            'password' => 'VendePro#2025',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'local_id']);

        $localId = DB::table('usuarios')->where('email', 'superadmin@vendepro.io')->value('local_id');
        $response->assertJsonPath('local_id', $localId);
    }
}
