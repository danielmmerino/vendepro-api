<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            'q' => ['nullable', 'string'],
            'activo' => ['nullable', 'in:0,1'],
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
        $activo = $request->query('activo');

        $params = [
            'empresa_id' => $empresa_id,
            'q' => $q,
            'activo' => $activo,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT c.*
FROM clientes c
WHERE c.empresa_id = :empresa_id
  AND (:activo IS NULL OR c.es_activo = :activo)
  AND (:q IS NULL OR (
        c.nombre LIKE CONCAT('%', :q, '%') OR
        c.identificacion LIKE CONCAT('%', :q, '%') OR
        c.email LIKE CONCAT('%', :q, '%') OR
        c.telefono LIKE CONCAT('%', :q, '%')
      ))
ORDER BY c.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM clientes c
WHERE c.empresa_id = :empresa_id
  AND (:activo IS NULL OR c.es_activo = :activo)
  AND (:q IS NULL OR (
        c.nombre LIKE CONCAT('%', :q, '%') OR
        c.identificacion LIKE CONCAT('%', :q, '%') OR
        c.email LIKE CONCAT('%', :q, '%') OR
        c.telefono LIKE CONCAT('%', :q, '%')
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

    public function store(StoreClienteRequest $request)
    {
        $data = $request->validated();
        $data['tipo_id'] = $data['tipo_id'] ?? 'CONSUMIDOR_FINAL';
        $data['identificacion'] = $data['identificacion'] ?? null;
        $data['es_activo'] = $data['es_activo'] ?? 1;

        if ($data['tipo_id'] !== 'CONSUMIDOR_FINAL') {
            if (!$data['identificacion']) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['identificacion' => ['requerido']],
                ], 422);
            }
        }

        if ($data['identificacion']) {
            $exists = DB::selectOne(
                "SELECT id FROM clientes WHERE empresa_id = :empresa_id AND identificacion = :identificacion",
                ['empresa_id' => $data['empresa_id'], 'identificacion' => $data['identificacion']]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO clientes
(empresa_id, tipo_id, identificacion, nombre, direccion, telefono, email, es_activo, created_at, updated_at)
VALUES
(:empresa_id, :tipo_id, :identificacion, :nombre, :direccion, :telefono, :email, :es_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                $data
            );
            $row = DB::selectOne("SELECT * FROM clientes WHERE id = LAST_INSERT_ID()");
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
            "SELECT * FROM clientes
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

    public function update(UpdateClienteRequest $request, $id)
    {
        $data = $request->validated();
        $data['tipo_id'] = $data['tipo_id'] ?? 'CONSUMIDOR_FINAL';
        $data['identificacion'] = $data['identificacion'] ?? null;
        $data['es_activo'] = $data['es_activo'] ?? 1;

        $row = DB::selectOne(
            "SELECT * FROM clientes WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $data['empresa_id']]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        if ($data['tipo_id'] !== 'CONSUMIDOR_FINAL' && !$data['identificacion']) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['identificacion' => ['requerido']],
            ], 422);
        }
        if ($data['identificacion'] && $data['identificacion'] !== $row->identificacion) {
            $exists = DB::selectOne(
                "SELECT id FROM clientes WHERE empresa_id = :empresa_id AND identificacion = :identificacion AND id <> :id",
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
            "UPDATE clientes
SET tipo_id = :tipo_id,
    identificacion = :identificacion,
    nombre = :nombre,
    direccion = :direccion,
    telefono = :telefono,
    email = :email,
    es_activo = :es_activo,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND empresa_id = :empresa_id",
            [
                'tipo_id' => $data['tipo_id'],
                'identificacion' => $data['identificacion'],
                'nombre' => $data['nombre'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'es_activo' => $data['es_activo'],
                'id' => $id,
                'empresa_id' => $data['empresa_id'],
            ]
        );
        $row = DB::selectOne(
            "SELECT * FROM clientes WHERE id = :id AND empresa_id = :empresa_id",
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
            "SELECT id FROM clientes WHERE id = :id AND empresa_id = :empresa_id",
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
  (SELECT COUNT(1) FROM facturas f WHERE f.cliente_id = :id) AS cnt_fact,
  (SELECT COUNT(1) FROM cxc cx WHERE cx.cliente_id = :id) AS cnt_cxc",
            ['id' => $id]
        );
        if (($refs->cnt_fact ?? 0) > 0 || ($refs->cnt_cxc ?? 0) > 0) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene referencias',
            ], 409);
        }

        DB::delete(
            "DELETE FROM clientes WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        return response()->json(null, 204);
    }
}
