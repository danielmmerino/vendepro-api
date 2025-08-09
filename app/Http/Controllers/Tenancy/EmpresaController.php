<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $q = $request->query('q');
        $activo = $request->query('activo');

        $params = [
            'q' => $q,
            'activo' => $activo,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT e.*
FROM empresas e
WHERE e.deleted_at IS NULL
  AND (:q IS NULL OR (
        e.ruc LIKE CONCAT('%', :q, '%') OR
        e.razon_social LIKE CONCAT('%', :q, '%') OR
        e.nombre_comercial LIKE CONCAT('%', :q, '%')
      ))
  AND (:activo IS NULL OR e.activo = :activo)
ORDER BY e.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM empresas e
WHERE e.deleted_at IS NULL
  AND (:q IS NULL OR (
        e.ruc LIKE CONCAT('%', :q, '%') OR
        e.razon_social LIKE CONCAT('%', :q, '%') OR
        e.nombre_comercial LIKE CONCAT('%', :q, '%')
      ))
  AND (:activo IS NULL OR e.activo = :activo)";

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
            'razon_social' => ['required'],
            'ruc' => ['required', 'size:13'],
            'nombre_comercial' => ['nullable'],
            'email' => ['nullable', 'email'],
            'telefono' => ['nullable'],
            'direccion' => ['nullable'],
            'pais' => ['nullable'],
            'provincia' => ['nullable'],
            'ciudad' => ['nullable'],
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
            "SELECT id FROM empresas WHERE ruc = :ruc AND deleted_at IS NULL",
            ['ruc' => $data['ruc']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'RUC ya registrado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO empresas
(razon_social, nombre_comercial, ruc, email, telefono, direccion, pais, provincia, ciudad, activo, created_at, updated_at)
VALUES
(:razon_social, :nombre_comercial, :ruc, :email, :telefono, :direccion, :pais, :provincia, :ciudad, :activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $empresa = DB::selectOne("SELECT * FROM empresas WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $empresa];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT e.*
FROM empresas e
WHERE e.id = :id AND e.deleted_at IS NULL
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
            'razon_social' => ['required'],
            'ruc' => ['required', 'size:13'],
            'nombre_comercial' => ['nullable'],
            'email' => ['nullable', 'email'],
            'telefono' => ['nullable'],
            'direccion' => ['nullable'],
            'pais' => ['nullable'],
            'provincia' => ['nullable'],
            'ciudad' => ['nullable'],
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
        $data['id'] = $id;

        $exists = DB::selectOne(
            "SELECT id FROM empresas WHERE ruc = :ruc AND id <> :id AND deleted_at IS NULL",
            ['ruc' => $data['ruc'], 'id' => $id]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'RUC ya registrado',
            ], 409);
        }

        return DB::transaction(function () use ($data, $id) {
            $affected = DB::update(
                "UPDATE empresas
SET razon_social = :razon_social,
    nombre_comercial = :nombre_comercial,
    ruc = :ruc,
    email = :email,
    telefono = :telefono,
    direccion = :direccion,
    pais = :pais,
    provincia = :provincia,
    ciudad = :ciudad,
    activo = :activo,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND deleted_at IS NULL",
                $data
            );
            if (!$affected) {
                return response()->json([
                    'error' => 'NotFound',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }
            $empresa = DB::selectOne("SELECT * FROM empresas WHERE id = :id", ['id' => $id]);
            return ['data' => (array) $empresa];
        });
    }

    public function destroy($id)
    {
        $affected = DB::update(
            "UPDATE empresas
SET deleted_at = CURRENT_TIMESTAMP,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND deleted_at IS NULL",
            ['id' => $id]
        );
        if (!$affected) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return response()->json(null, 204);
    }
}
