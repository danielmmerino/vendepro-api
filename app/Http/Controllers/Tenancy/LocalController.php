<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocalController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $q = $request->query('q');
        $activo = $request->query('activo');

        $params = [
            'empresa_id' => $empresa_id,
            'q' => $q,
            'activo' => $activo,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT l.*
FROM locales l
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE l.deleted_at IS NULL
  AND (:empresa_id IS NULL OR l.empresa_id = :empresa_id)
  AND (:q IS NULL OR (
        l.nombre LIKE CONCAT('%', :q, '%') OR
        l.codigo_establecimiento LIKE CONCAT('%', :q, '%') OR
        l.codigo_punto_emision LIKE CONCAT('%', :q, '%')
      ))
  AND (:activo IS NULL OR l.activo = :activo)
ORDER BY l.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM locales l
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE l.deleted_at IS NULL
  AND (:empresa_id IS NULL OR l.empresa_id = :empresa_id)
  AND (:q IS NULL OR (
        l.nombre LIKE CONCAT('%', :q, '%') OR
        l.codigo_establecimiento LIKE CONCAT('%', :q, '%') OR
        l.codigo_punto_emision LIKE CONCAT('%', :q, '%')
      ))
  AND (:activo IS NULL OR l.activo = :activo)";

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
            'direccion' => ['nullable'],
            'telefono' => ['nullable'],
            'codigo_establecimiento' => ['required'],
            'codigo_punto_emision' => ['required'],
            'secuencial_factura' => ['required', 'integer'],
            'secuencial_nc' => ['required', 'integer'],
            'secuencial_retencion' => ['required', 'integer'],
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

        $empresa = DB::selectOne("SELECT id FROM empresas WHERE id = :id AND deleted_at IS NULL", ['id' => $data['empresa_id']]);
        if (!$empresa) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['empresa_id' => ['empresa inexistente']],
            ], 422);
        }

        $exists = DB::selectOne(
            "SELECT id FROM locales WHERE empresa_id = :empresa_id AND codigo_establecimiento = :codigo_establecimiento AND codigo_punto_emision = :codigo_punto_emision AND deleted_at IS NULL",
            [
                'empresa_id' => $data['empresa_id'],
                'codigo_establecimiento' => $data['codigo_establecimiento'],
                'codigo_punto_emision' => $data['codigo_punto_emision'],
            ]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Combinación SRI ya registrada',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO locales
(empresa_id, nombre, direccion, telefono, codigo_establecimiento, codigo_punto_emision,
 secuencial_factura, secuencial_nc, secuencial_retencion, activo, created_at, updated_at)
VALUES
(:empresa_id, :nombre, :direccion, :telefono, :codigo_establecimiento, :codigo_punto_emision,
 :secuencial_factura, :secuencial_nc, :secuencial_retencion, :activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $local = DB::selectOne("SELECT * FROM locales WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $local];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT l.*
FROM locales l
JOIN empresas e ON e.id = l.empresa_id AND e.deleted_at IS NULL
WHERE l.id = :id AND l.deleted_at IS NULL
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
            'nombre' => ['required'],
            'direccion' => ['nullable'],
            'telefono' => ['nullable'],
            'codigo_establecimiento' => ['required'],
            'codigo_punto_emision' => ['required'],
            'secuencial_factura' => ['required', 'integer'],
            'secuencial_nc' => ['required', 'integer'],
            'secuencial_retencion' => ['required', 'integer'],
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
            "SELECT id FROM locales WHERE empresa_id = (SELECT empresa_id FROM locales WHERE id = :id) AND codigo_establecimiento = :codigo_establecimiento AND codigo_punto_emision = :codigo_punto_emision AND id <> :id AND deleted_at IS NULL",
            [
                'codigo_establecimiento' => $data['codigo_establecimiento'],
                'codigo_punto_emision' => $data['codigo_punto_emision'],
                'id' => $id,
            ]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Combinación SRI ya registrada',
            ], 409);
        }

        return DB::transaction(function () use ($data, $id) {
            $affected = DB::update(
                "UPDATE locales
SET nombre = :nombre,
    direccion = :direccion,
    telefono = :telefono,
    codigo_establecimiento = :codigo_establecimiento,
    codigo_punto_emision  = :codigo_punto_emision,
    secuencial_factura    = :secuencial_factura,
    secuencial_nc         = :secuencial_nc,
    secuencial_retencion  = :secuencial_retencion,
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
            $local = DB::selectOne("SELECT * FROM locales WHERE id = :id", ['id' => $id]);
            return ['data' => (array) $local];
        });
    }

    public function destroy($id)
    {
        $affected = DB::update(
            "UPDATE locales
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
