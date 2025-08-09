<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MetodoPagoController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $activo = $request->query('activo');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'activo' => $activo,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT mp.*
FROM metodos_pago mp
WHERE ((:empresa_id IS NULL AND mp.empresa_id IS NULL) OR (mp.empresa_id = :empresa_id OR mp.empresa_id IS NULL))
  AND (:activo IS NULL OR mp.activo = :activo)
  AND (:q IS NULL OR (mp.nombre LIKE CONCAT('%', :q, '%')))
ORDER BY mp.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM metodos_pago mp
WHERE ((:empresa_id IS NULL AND mp.empresa_id IS NULL) OR (mp.empresa_id = :empresa_id OR mp.empresa_id IS NULL))
  AND (:activo IS NULL OR mp.activo = :activo)
  AND (:q IS NULL OR (mp.nombre LIKE CONCAT('%', :q, '%')))";

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
            'empresa_id' => ['nullable', 'integer'],
            'nombre' => ['required'],
            'activo' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['activo'] = $data['activo'] ?? 1;

        $exists = DB::selectOne(
            "SELECT id FROM metodos_pago WHERE ((empresa_id IS NULL AND :empresa_id IS NULL) OR empresa_id = :empresa_id) AND nombre = :nombre",
            ['empresa_id' => $data['empresa_id'] ?? null, 'nombre' => $data['nombre']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO metodos_pago (empresa_id, nombre, activo, created_at, updated_at)
VALUES (:empresa_id, :nombre, :activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [
                    'empresa_id' => $data['empresa_id'] ?? null,
                    'nombre' => $data['nombre'],
                    'activo' => $data['activo'],
                ]
            );
            $row = DB::selectOne("SELECT * FROM metodos_pago WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne("SELECT * FROM metodos_pago WHERE id = :id LIMIT 1", ['id' => $id]);
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
            'nombre' => ['required'],
            'activo' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $row = DB::selectOne("SELECT * FROM metodos_pago WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $data = $validator->validated();
        $data['activo'] = $data['activo'] ?? 1;
        if ($data['nombre'] !== $row->nombre) {
            $exists = DB::selectOne(
                "SELECT id FROM metodos_pago WHERE ((empresa_id IS NULL AND :empresa_id IS NULL) OR empresa_id = :empresa_id) AND nombre = :nombre AND id <> :id",
                ['empresa_id' => $row->empresa_id, 'nombre' => $data['nombre'], 'id' => $id]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        DB::update(
            "UPDATE metodos_pago
SET nombre = :nombre,
    activo = :activo,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id",
            [
                'nombre' => $data['nombre'],
                'activo' => $data['activo'],
                'id' => $id,
            ]
        );
        $row = DB::selectOne("SELECT * FROM metodos_pago WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $row = DB::selectOne("SELECT id FROM metodos_pago WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $ref1 = DB::selectOne("SELECT id FROM pagos_venta WHERE metodo_pago_id = :id LIMIT 1", ['id' => $id]);
        $ref2 = DB::selectOne("SELECT id FROM pagos_proveedor WHERE metodo_pago_id = :id LIMIT 1", ['id' => $id]);
        if ($ref1 || $ref2) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene relaciones',
            ], 409);
        }

        DB::delete("DELETE FROM metodos_pago WHERE id = :id", ['id' => $id]);
        return response()->json(null, 204);
    }
}

