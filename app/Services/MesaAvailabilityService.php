<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MesaAvailabilityService
{
    public function findConflict(string $mesaId, string $inicio, string $fin, ?string $excludeId = null): ?object
    {
        $query = DB::table('reservas as r')
            ->select('r.id', 'r.inicio', 'r.fin', 'r.estado')
            ->where('r.mesa_id', $mesaId)
            ->whereIn('r.estado', ['pendiente', 'confirmada'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('r.inicio', '<', $fin)
                    ->where('r.fin', '>', $inicio);
            })
            ->orderBy('r.inicio', 'asc');

        if ($excludeId) {
            $query->where('r.id', '!=', $excludeId);
        }

        return $query->first();
    }

    public function isAvailable(string $mesaId, string $inicio, string $fin, ?string $excludeId = null): bool
    {
        return $this->findConflict($mesaId, $inicio, $fin, $excludeId) === null;
    }
}
