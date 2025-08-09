<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            'q' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }

        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = (int) $request->query('empresa_id');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT p.*
FROM proveedores p
WHERE p.empresa_id = :empresa_id
  AND (:q IS NULL OR (
        p.nombre LIKE CONCAT('%', :q, '%') OR
        p.identificacion LIKE CONCAT('%', :q, '%') OR
        p.email LIKE CONCAT('%', :q, '%') OR
        p.telefono LIKE CONCAT('%', :q, '%')
      ))
ORDER BY p.id DESC
LIMIT :per OFFSET :off";
        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM proveedores p
WHERE p.empresa_id = :empresa_id
  AND (:q IS NULL OR (
        p.nombre LIKE CONCAT('%', :q, '%') OR
        p.identificacion LIKE CONCAT('%', :q, '%') OR
        p.email LIKE CONCAT('%', :q, '%') OR
        p.telefono LIKE CONCAT('%', :q, '%')
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

    public function store(StoreProveedorRequest $request)
    {
        $data = $request->validated();

        $exists = DB::selectOne(
            "SELECT id FROM proveedores WHERE empresa_id = :empresa_id AND identificacion = :identificacion",
            ['empresa_id' => $data['empresa_id'], 'identificacion' => $data['identificacion']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO proveedores
(empresa_id, identificacion, nombre, direccion, telefono, email, created_at, updated_at)
VALUES
(:empresa_id, :identificacion, :nombre, :direccion, :telefono, :email, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $row = DB::selectOne("SELECT * FROM proveedores WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = (int) $request->query('empresa_id');
        $row = DB::selectOne(
            "SELECT * FROM proveedores
WHERE id = :id AND empresa_id = :empresa_id
LIMIT 1",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return ['data' => (array) $row];
    }

    public function update(UpdateProveedorRequest $request, $id)
    {
        $data = $request->validated();

        $row = DB::selectOne(
            "SELECT * FROM proveedores WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $data['empresa_id']]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        if ($data['identificacion'] !== $row->identificacion) {
            $exists = DB::selectOne(
                "SELECT id FROM proveedores WHERE empresa_id = :empresa_id AND identificacion = :identificacion AND id <> :id",
                ['empresa_id' => $data['empresa_id'], 'identificacion' => $data['identificacion'], 'id' => $id]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        DB::update(
            "UPDATE proveedores
SET identificacion = :identificacion,
    nombre = :nombre,
    direccion = :direccion,
    telefono = :telefono,
    email = :email,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND empresa_id = :empresa_id",
            [
                'identificacion' => $data['identificacion'],
                'nombre' => $data['nombre'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'id' => $id,
                'empresa_id' => $data['empresa_id'],
            ]
        );
        $row = DB::selectOne(
            "SELECT * FROM proveedores WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $data['empresa_id']]
        );
        return ['data' => (array) $row];
    }

    public function destroy(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = (int) $request->query('empresa_id');
        $row = DB::selectOne(
            "SELECT id FROM proveedores WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $refs = DB::selectOne(
            "SELECT
  (SELECT COUNT(1) FROM compras c WHERE c.proveedor_id = :id) AS cnt_comp,
  (SELECT COUNT(1) FROM cxp x WHERE x.proveedor_id = :id) AS cnt_cxp",
            ['id' => $id]
        );
        if (($refs->cnt_comp ?? 0) > 0 || ($refs->cnt_cxp ?? 0) > 0) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene referencias',
            ], 409);
        }

        DB::delete(
            "DELETE FROM proveedores WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        return response()->json(null, 204);
    }
}
