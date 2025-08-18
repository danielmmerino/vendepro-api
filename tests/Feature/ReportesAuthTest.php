<?php

namespace Tests\Feature;

use App\Models\User;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->getJson('/v1/dashboard/resumen');
        $response->assertStatus(401);
    }

    public function test_dashboard_requires_permission(): void
    {
        $this->withoutMiddleware(JwtMiddleware::class);
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->getJson('/v1/dashboard/resumen');
        $response->assertStatus(403);
    }
}
