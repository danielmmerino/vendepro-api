<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaProductoController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $padre_id = $request->query('padre_id');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'padre_id' => $padre_id,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT c.*
FROM categorias_producto c
WHERE c.empresa_id = :empresa_id
  AND (:padre_id IS NULL OR c.padre_id = :padre_id)
  AND (:q IS NULL OR c.nombre LIKE CONCAT('%', :q, '%'))
ORDER BY COALESCE(c.orden, 9999) ASC, c.nombre ASC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM categorias_producto c
WHERE c.empresa_id = :empresa_id
  AND (:padre_id IS NULL OR c.padre_id = :padre_id)
  AND (:q IS NULL OR c.nombre LIKE CONCAT('%', :q, '%'))";

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
            'nombre' => ['required'],
            'padre_id' => ['nullable', 'integer'],
            'orden' => ['nullable', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        // validate padre_id
        if (!empty($data['padre_id'])) {
            $parent = DB::selectOne("SELECT id FROM categorias_producto WHERE id = :padre_id AND empresa_id = :empresa_id", [
                'padre_id' => $data['padre_id'],
                'empresa_id' => $data['empresa_id'],
            ]);
            if (!$parent) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['padre_id' => ['Inválido']],
                ], 422);
            }
        }

        $exists = DB::selectOne(
            "SELECT id FROM categorias_producto WHERE empresa_id = :empresa_id AND nombre = :nombre",
            ['empresa_id' => $data['empresa_id'], 'nombre' => $data['nombre']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO categorias_producto (empresa_id, nombre, padre_id, orden, created_at, updated_at)
VALUES (:empresa_id, :nombre, :padre_id, :orden, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [
                    'empresa_id' => $data['empresa_id'],
                    'nombre' => $data['nombre'],
                    'padre_id' => $data['padre_id'] ?? null,
                    'orden' => $data['orden'] ?? null,
                ]
            );
            $row = DB::selectOne("SELECT * FROM categorias_producto WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne("SELECT * FROM categorias_producto WHERE id = :id LIMIT 1", ['id' => $id]);
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
            'padre_id' => ['nullable', 'integer'],
            'orden' => ['nullable', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $row = DB::selectOne("SELECT * FROM categorias_producto WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $data = $validator->validated();
        // padre_id validation
        if (!empty($data['padre_id'])) {
            if ($data['padre_id'] == $id) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['padre_id' => ['Inválido']],
                ], 422);
            }
            $parent = DB::selectOne("SELECT id FROM categorias_producto WHERE id = :padre_id AND empresa_id = :empresa_id", [
                'padre_id' => $data['padre_id'],
                'empresa_id' => $row->empresa_id,
            ]);
            if (!$parent) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['padre_id' => ['Inválido']],
                ], 422);
            }
        }

        if ($data['nombre'] !== $row->nombre) {
            $exists = DB::selectOne(
                "SELECT id FROM categorias_producto WHERE empresa_id = :empresa_id AND nombre = :nombre AND id <> :id",
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
            "UPDATE categorias_producto
SET nombre = :nombre,
    padre_id = :padre_id,
    orden = :orden,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id",
            [
                'nombre' => $data['nombre'],
                'padre_id' => $data['padre_id'] ?? null,
                'orden' => $data['orden'] ?? null,
                'id' => $id,
            ]
        );
        $row = DB::selectOne("SELECT * FROM categorias_producto WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $row = DB::selectOne("SELECT id FROM categorias_producto WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $child = DB::selectOne("SELECT id FROM categorias_producto WHERE padre_id = :id LIMIT 1", ['id' => $id]);
        if ($child) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene relaciones',
            ], 409);
        }
        $ref = DB::selectOne("SELECT id FROM productos WHERE categoria_id = :id LIMIT 1", ['id' => $id]);
        if ($ref) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene relaciones',
            ], 409);
        }

        DB::delete("DELETE FROM categorias_producto WHERE id = :id", ['id' => $id]);
        return response()->json(null, 204);
    }
}

