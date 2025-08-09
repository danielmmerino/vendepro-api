<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuscripcionLocalController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $local_id = $request->query('local_id');
        $estado = $request->query('estado');
        $vigentes = $request->query('vigentes');

        $params = [
            'local_id' => $local_id,
            'estado' => $estado,
            'vigentes' => $vigentes,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT sl.*
FROM suscripciones_locales sl
JOIN suscripciones s ON s.id = sl.suscripcion_id
JOIN locales l ON l.id = sl.local_id AND l.deleted_at IS NULL
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE (:local_id IS NULL OR sl.local_id = :local_id)
  AND (:estado IS NULL OR sl.estado = :estado)
  AND (:vigentes IS NULL OR (
        sl.estado = 'ACTIVA' AND
        sl.fecha_inicio <= CURRENT_DATE() AND
        (sl.fecha_fin IS NULL OR sl.fecha_fin >= CURRENT_DATE())
      ))
ORDER BY sl.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM suscripciones_locales sl
JOIN suscripciones s ON s.id = sl.suscripcion_id
JOIN locales l ON l.id = sl.local_id AND l.deleted_at IS NULL
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE (:local_id IS NULL OR sl.local_id = :local_id)
  AND (:estado IS NULL OR sl.estado = :estado)
  AND (:vigentes IS NULL OR (
        sl.estado = 'ACTIVA' AND
        sl.fecha_inicio <= CURRENT_DATE() AND
        (sl.fecha_fin IS NULL OR sl.fecha_fin >= CURRENT_DATE())
      ))";

        $total = DB::selectOne($countSql, $params)->total ?? 0;

        return [
            'data' => array_map(fn($r) => (array) $r, $rows),
            'pagination' => [
                'page' => $page,
                'per_page' => $per,
                'total' => (int) $total,
            ],
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suscripcion_id' => ['required', 'integer'],
            'local_id' => ['required', 'integer'],
            'estado' => ['required'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        $sus = DB::selectOne("SELECT id FROM suscripciones WHERE id = :id", ['id' => $data['suscripcion_id']]);
        $loc = DB::selectOne("SELECT id FROM locales WHERE id = :id AND deleted_at IS NULL", ['id' => $data['local_id']]);
        if (!$sus || !$loc) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['suscripcion_id' => !$sus ? ['suscripcion inexistente'] : null, 'local_id' => !$loc ? ['local inexistente'] : null],
            ], 422);
        }

        $exists = DB::selectOne(
            "SELECT id FROM suscripciones_locales WHERE suscripcion_id = :suscripcion_id AND local_id = :local_id",
            ['suscripcion_id' => $data['suscripcion_id'], 'local_id' => $data['local_id']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Registro duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO suscripciones_locales
(suscripcion_id, local_id, estado, fecha_inicio, fecha_fin, created_at, updated_at)
VALUES
(:suscripcion_id, :local_id, :estado, :fecha_inicio, :fecha_fin, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $row = DB::selectOne("SELECT * FROM suscripciones_locales WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT sl.*
FROM suscripciones_locales sl
JOIN suscripciones s ON s.id = sl.suscripcion_id
JOIN locales l ON l.id = sl.local_id AND l.deleted_at IS NULL
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE sl.id = :id
LIMIT 1",
            ['id' => $id]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return ['data' => (array) $row];
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => ['required'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['id'] = $id;

        return DB::transaction(function () use ($data, $id) {
            $affected = DB::update(
                "UPDATE suscripciones_locales
SET estado = :estado,
    fecha_inicio = :fecha_inicio,
    fecha_fin = :fecha_fin,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id",
                $data
            );
            if (!$affected) {
                return response()->json([
                    'error' => 'NotFound',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }
            $row = DB::selectOne("SELECT * FROM suscripciones_locales WHERE id = :id", ['id' => $id]);
            return ['data' => (array) $row];
        });
    }
}
