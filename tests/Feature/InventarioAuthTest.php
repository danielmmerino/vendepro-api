<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_requires_auth(): void
    {
        $this->getJson('/v1/stock')->assertStatus(401);
    }
}
