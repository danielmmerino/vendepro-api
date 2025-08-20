<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $localId = $request->input('local_id')
            ?? $request->header('X-Local-Id')
            ?? $user->local_id
            ?? null;

        $empresaId = $request->input('empresa_id')
            ?? $user->empresa_id
            ?? null;

        if (!$localId && !$empresaId) {
            return response()->json(['message' => 'Subscription inactive'], 403);
        }

        try {
            if ($localId) {
                $row = DB::selectOne(
                    "SELECT
  CASE
    WHEN e.activo = 1
     AND l.activo = 1
     AND EXISTS (
       SELECT 1
       FROM suscripciones s
       WHERE s.empresa_id = e.id
         AND s.estado = 'ACTIVA'
         AND s.fecha_inicio <= CURRENT_DATE()
         AND (s.fecha_fin IS NULL OR s.fecha_fin >= CURRENT_DATE())
     )
     AND EXISTS (
       SELECT 1
       FROM suscripciones_locales sl
       JOIN suscripciones s2 ON s2.id = sl.suscripcion_id
       WHERE sl.local_id = l.id
         AND sl.estado = 'ACTIVA'
         AND sl.fecha_inicio <= CURRENT_DATE()
         AND (sl.fecha_fin IS NULL OR sl.fecha_fin >= CURRENT_DATE())
         AND s2.estado = 'ACTIVA'
         AND s2.fecha_inicio <= CURRENT_DATE()
         AND (s2.fecha_fin IS NULL OR s2.fecha_fin >= CURRENT_DATE())
     )
    THEN 1 ELSE 0
  END AS vigente
FROM locales l
JOIN empresas e ON e.id = l.empresa_id
WHERE l.id = :local_id
  AND e.deleted_at IS NULL
  AND l.deleted_at IS NULL
LIMIT 1",
                    ['local_id' => $localId]
                );
            } else {
                $row = DB::selectOne(
                    "SELECT
  CASE
    WHEN e.activo = 1
     AND EXISTS (
       SELECT 1
       FROM suscripciones s
       WHERE s.empresa_id = e.id
         AND s.estado = 'ACTIVA'
         AND s.fecha_inicio <= CURRENT_DATE()
         AND (s.fecha_fin IS NULL OR s.fecha_fin >= CURRENT_DATE())
     )
    THEN 1 ELSE 0
  END AS vigente
FROM empresas e
WHERE e.id = :empresa_id
  AND e.deleted_at IS NULL
LIMIT 1",
                    ['empresa_id' => $empresaId]
                );
            }
        } catch (QueryException $e) {
            if ($e->getCode() !== '42S02') {
                throw $e;
            }
            $row = (object) ['vigente' => 1];
        }

        if (!$row || $row->vigente != 1) {
            return response()->json(['message' => 'Subscription inactive'], 403);
        }

        return $next($request);
    }
}
