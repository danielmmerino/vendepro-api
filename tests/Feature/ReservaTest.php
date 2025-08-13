<?php

namespace Tests\Feature;

use App\Models\Reserva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_reserva_success(): void
    {
        $mesaId = (string) Str::uuid();

        $response = $this->postJson('/api/v1/reservas', [
            'mesa_id' => $mesaId,
            'inicio' => '2024-01-01 10:00:00',
            'fin' => '2024-01-01 11:00:00',
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_create_reserva_conflict(): void
    {
        $mesaId = (string) Str::uuid();

        $this->postJson('/api/v1/reservas', [
            'mesa_id' => $mesaId,
            'inicio' => '2024-01-01 10:00:00',
            'fin' => '2024-01-01 11:00:00',
        ]);

        $response = $this->postJson('/api/v1/reservas', [
            'mesa_id' => $mesaId,
            'inicio' => '2024-01-01 10:30:00',
            'fin' => '2024-01-01 11:30:00',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_confirm_reserva_success(): void
    {
        $reserva = Reserva::create([
            'mesa_id' => (string) Str::uuid(),
            'inicio' => '2024-01-01 10:00:00',
            'fin' => '2024-01-01 11:00:00',
        ]);

        $response = $this->postJson("/api/v1/reservas/{$reserva->id}/confirmar");

        $response->assertOk();
        $this->assertDatabaseHas('reservas', ['id' => $reserva->id, 'estado' => 'confirmada']);
    }

    public function test_confirm_reserva_conflict(): void
    {
        $mesaId = (string) Str::uuid();

        Reserva::create([
            'mesa_id' => $mesaId,
            'inicio' => '2024-01-01 10:00:00',
            'fin' => '2024-01-01 11:00:00',
            'estado' => 'confirmada',
        ]);

        $reservaPendiente = Reserva::create([
            'mesa_id' => $mesaId,
            'inicio' => '2024-01-01 10:30:00',
            'fin' => '2024-01-01 11:30:00',
        ]);

        $response = $this->postJson("/api/v1/reservas/{$reservaPendiente->id}/confirmar");

        $response->assertStatus(422);
        $this->assertDatabaseHas('reservas', ['id' => $reservaPendiente->id, 'estado' => 'pendiente']);
    }
}
