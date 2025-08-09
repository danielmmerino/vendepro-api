<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;
        $q = $request->query('q');
        $params = [
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT r.id, r.codigo, r.nombre, r.descripcion, r.created_at, r.updated_at
FROM roles r
WHERE (:q IS NULL OR (r.codigo LIKE CONCAT('%', :q, '%') OR r.nombre LIKE CONCAT('%', :q, '%')))
ORDER BY r.id DESC
LIMIT :per OFFSET :off";
        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM roles r
WHERE (:q IS NULL OR (r.codigo LIKE CONCAT('%', :q, '%') OR r.nombre LIKE CONCAT('%', :q, '%')))";
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
            'codigo' => ['required'],
            'nombre' => ['required'],
            'descripcion' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        $exists = DB::selectOne("SELECT id FROM roles WHERE codigo = :codigo", ['codigo' => $data['codigo']]);
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        DB::insert(
            "INSERT INTO roles (codigo, nombre, descripcion, created_at, updated_at)
VALUES (:codigo, :nombre, :descripcion, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $data
        );
        $row = DB::selectOne("SELECT * FROM roles WHERE id = LAST_INSERT_ID()");
        return ['data' => (array) $row];
    }

    public function show($id)
    {
        $row = DB::selectOne("SELECT * FROM roles WHERE id = :id LIMIT 1", ['id' => $id]);
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
            'codigo' => ['required'],
            'nombre' => ['required'],
            'descripcion' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['id'] = $id;

        $exists = DB::selectOne("SELECT id FROM roles WHERE codigo = :codigo AND id <> :id", ['codigo' => $data['codigo'], 'id' => $id]);
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        $affected = DB::update(
            "UPDATE roles
SET codigo = :codigo,
    nombre = :nombre,
    descripcion = :descripcion,
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
        $row = DB::selectOne("SELECT * FROM roles WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $affected = DB::delete("DELETE FROM roles WHERE id = :id", ['id' => $id]);
        if (!$affected) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return response()->json(null, 204);
    }
}
