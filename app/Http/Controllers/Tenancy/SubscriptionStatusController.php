<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $local_id = $validator->validated()['local_id'];

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
  END AS vigente,
  e.id AS empresa_id,
  l.id AS local_id
FROM locales l
JOIN empresas e ON e.id = l.empresa_id
WHERE l.id = :local_id
  AND e.deleted_at IS NULL
  AND l.deleted_at IS NULL
LIMIT 1",
            ['local_id' => $local_id]
        );

        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return ['data' => (array) $row];
    }
}
