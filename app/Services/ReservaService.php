<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReservaService
{
    /**
     * Check if a table is available between two datetimes.
     *
     * @param string $mesaId Table identifier
     * @param string $inicio Start datetime (Y-m-d H:i:s)
     * @param string $fin End datetime (Y-m-d H:i:s)
     * @param string|null $excluirId Reservation ID to exclude
     * @return bool true if the table is available, false otherwise
     */
    public function mesaDisponible(string $mesaId, string $inicio, string $fin, ?string $excluirId = null): bool
    {
        $query = DB::table('reservas as r')
            ->select('r.id', 'r.inicio', 'r.fin', 'r.estado')
            ->where('r.mesa_id', $mesaId)
            ->whereIn('r.estado', ['pendiente', 'confirmada'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('r.inicio', '<', $fin)
                    ->where('r.fin', '>', $inicio);
            });

        if ($excluirId) {
            $query->where('r.id', '<>', $excluirId);
        }

        return $query->orderBy('r.inicio', 'asc')->first() === null;
    }
}
