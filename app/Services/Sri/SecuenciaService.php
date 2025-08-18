<?php

namespace App\Services\Sri;

use App\Models\Sri\Secuencia;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SecuenciaService
{
    /**
     * Obtiene y bloquea la siguiente secuencia.
     */
    public function next(string $emisorId, string $establecimiento, string $punto, string $tipo): int
    {
        return DB::transaction(function () use ($emisorId, $establecimiento, $punto, $tipo) {
            $row = DB::table('sri_secuencias as s')
                ->join('sri_puntos_emision as pe', 'pe.id', '=', 's.punto_emision_id')
                ->join('sri_establecimientos as e', 'e.id', '=', 'pe.establecimiento_id')
                ->where('e.emisor_id', $emisorId)
                ->where('e.codigo', $establecimiento)
                ->where('pe.codigo', $punto)
                ->where('s.tipo', $tipo)
                ->lockForUpdate()
                ->select('s.id','s.actual','pe.sec_hasta')
                ->first();

            if (!$row) {
                throw new RuntimeException('Secuencia no encontrada');
            }

            $nuevo = $row->actual + 1;
            if ($nuevo > $row->sec_hasta) {
                throw new RuntimeException('Secuencia agotada');
            }

            DB::table('sri_secuencias')->where('id', $row->id)->update(['actual' => $nuevo, 'updated_at' => now()]);

            return $nuevo;
        });
    }

    public function current(?string $emisorId, string $establecimiento, string $punto, string $tipo): ?int
    {
        $row = DB::table('sri_secuencias as s')
            ->join('sri_puntos_emision as pe', 'pe.id', '=', 's.punto_emision_id')
            ->join('sri_establecimientos as e', 'e.id', '=', 'pe.establecimiento_id')
            ->when($emisorId, fn($q) => $q->where('e.emisor_id', $emisorId))
            ->where('e.codigo', $establecimiento)
            ->where('pe.codigo', $punto)
            ->where('s.tipo', $tipo)
            ->select('s.actual')
            ->first();
        return $row? $row->actual : null;
    }
}
