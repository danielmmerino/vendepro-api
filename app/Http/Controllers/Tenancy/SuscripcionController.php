<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuscripcionController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $estado = $request->query('estado');
        $vigentes = $request->query('vigentes');

        $params = [
            'empresa_id' => $empresa_id,
            'estado' => $estado,
            'vigentes' => $vigentes,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT s.*
FROM suscripciones s
JOIN empresas e ON e.id = s.empresa_id AND e.deleted_at IS NULL
WHERE (:empresa_id IS NULL OR s.empresa_id = :empresa_id)
  AND (:estado IS NULL OR s.estado = :estado)
  AND (:vigentes IS NULL OR (
        s.estado = 'ACTIVA' AND
        s.fecha_inicio <= CURRENT_DATE() AND
        (s.fecha_fin IS NULL OR s.fecha_fin >= CURRENT_DATE())
      ))
ORDER BY s.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM suscripciones s
JOIN empresas e ON e.id = s.empresa_id AND e.deleted_at IS NULL
WHERE (:empresa_id IS NULL OR s.empresa_id = :empresa_id)
  AND (:estado IS NULL OR s.estado = :estado)
  AND (:vigentes IS NULL OR (
        s.estado = 'ACTIVA' AND
        s.fecha_inicio <= CURRENT_DATE() AND
        (s.fecha_fin IS NULL OR s.fecha_fin >= CURRENT_DATE())
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
            'empresa_id' => ['required', 'integer'],
            'plan_id' => ['required', 'integer'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required'],
            'ultimo_pago' => ['nullable', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        $empresa = DB::selectOne("SELECT id FROM empresas WHERE id = :id AND deleted_at IS NULL", ['id' => $data['empresa_id']]);
        if (!$empresa) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['empresa_id' => ['empresa inexistente']],
            ], 422);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO suscripciones
(empresa_id, plan_id, fecha_inicio, fecha_fin, estado, ultimo_pago, created_at, updated_at)
VALUES
(:empresa_id, :plan_id, :fecha_inicio, :fecha_fin, :estado, :ultimo_pago, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $row = DB::selectOne("SELECT * FROM suscripciones WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT s.*
FROM suscripciones s
JOIN empresas e ON e.id = s.empresa_id AND e.deleted_at IS NULL
WHERE s.id = :id
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
            'plan_id' => ['required', 'integer'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required'],
            'ultimo_pago' => ['nullable', 'date'],
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
                "UPDATE suscripciones
SET plan_id = :plan_id,
    fecha_inicio = :fecha_inicio,
    fecha_fin = :fecha_fin,
    estado = :estado,
    ultimo_pago = :ultimo_pago,
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
            $row = DB::selectOne("SELECT * FROM suscripciones WHERE id = :id", ['id' => $id]);
            return ['data' => (array) $row];
        });
    }
}
