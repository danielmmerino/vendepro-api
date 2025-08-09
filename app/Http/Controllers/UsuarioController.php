<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $local_id = $request->query('local_id');
        $activo = $request->query('activo');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'local_id' => $local_id,
            'activo' => $activo,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT
  u.id, u.empresa_id, u.local_id, u.nombre, u.email, u.telefono, u.activo,
  u.debe_cambiar_password, u.ultimo_acceso, u.created_at, u.updated_at,
  (
    SELECT JSON_ARRAYAGG(r.codigo)
    FROM usuario_roles ur
    JOIN roles r ON r.id = ur.rol_id
    WHERE ur.usuario_id = u.id
  ) AS roles
FROM usuarios u
WHERE u.deleted_at IS NULL
  AND (:empresa_id IS NULL OR u.empresa_id = :empresa_id)
  AND (:local_id IS NULL OR u.local_id = :local_id)
  AND (:activo IS NULL OR u.activo = :activo)
  AND (:q IS NULL OR (u.nombre LIKE CONCAT('%', :q, '%') OR u.email LIKE CONCAT('%', :q, '%')))
ORDER BY u.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM usuarios u
WHERE u.deleted_at IS NULL
  AND (:empresa_id IS NULL OR u.empresa_id = :empresa_id)
  AND (:local_id IS NULL OR u.local_id = :local_id)
  AND (:activo IS NULL OR u.activo = :activo)
  AND (:q IS NULL OR (u.nombre LIKE CONCAT('%', :q, '%') OR u.email LIKE CONCAT('%', :q, '%')))";

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
            'local_id' => ['nullable', 'integer'],
            'nombre' => ['required'],
            'email' => ['required', 'email'],
            'password_hash' => ['required'],
            'telefono' => ['nullable'],
            'activo' => ['boolean'],
            'debe_cambiar_password' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['activo'] = $data['activo'] ?? 1;
        $data['debe_cambiar_password'] = $data['debe_cambiar_password'] ?? 0;

        $empresa = DB::selectOne("SELECT id FROM empresas WHERE id = :id AND deleted_at IS NULL", ['id' => $data['empresa_id']]);
        if (!$empresa) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['empresa_id' => ['empresa inexistente']],
            ], 422);
        }

        $exists = DB::selectOne(
            "SELECT id FROM usuarios WHERE empresa_id = :empresa_id AND email = :email AND deleted_at IS NULL",
            ['empresa_id' => $data['empresa_id'], 'email' => $data['email']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        DB::insert(
            "INSERT INTO usuarios
(empresa_id, local_id, nombre, email, password_hash, telefono, activo, debe_cambiar_password, token_version, created_at, updated_at)
VALUES
(:empresa_id, :local_id, :nombre, :email, :password_hash, :telefono, :activo, :debe_cambiar_password, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $data
        );

        $row = DB::selectOne("SELECT u.id, u.empresa_id, u.local_id, u.nombre, u.email, u.telefono, u.activo,
       u.debe_cambiar_password, u.ultimo_acceso, u.created_at, u.updated_at,
       JSON_ARRAY() AS roles
FROM usuarios u
WHERE u.id = LAST_INSERT_ID()");

        return ['data' => (array) $row];
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT
  u.id, u.empresa_id, u.local_id, u.nombre, u.email, u.telefono, u.activo,
  u.debe_cambiar_password, u.ultimo_acceso, u.created_at, u.updated_at,
  (
    SELECT JSON_ARRAYAGG(r.codigo)
    FROM usuario_roles ur
    JOIN roles r ON r.id = ur.rol_id
    WHERE ur.usuario_id = u.id
  ) AS roles
FROM usuarios u
WHERE u.id = :id AND u.deleted_at IS NULL
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
            'local_id' => ['nullable', 'integer'],
            'nombre' => ['required'],
            'email' => ['required', 'email'],
            'telefono' => ['nullable'],
            'activo' => ['boolean'],
            'debe_cambiar_password' => ['boolean'],
            'password_hash' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['id'] = $id;

        $user = DB::selectOne("SELECT empresa_id, email FROM usuarios WHERE id = :id AND deleted_at IS NULL", ['id' => $id]);
        if (!$user) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $exists = DB::selectOne(
            "SELECT id FROM usuarios WHERE empresa_id = :empresa_id AND email = :email AND id <> :id AND deleted_at IS NULL",
            ['empresa_id' => $user->empresa_id, 'email' => $data['email'], 'id' => $id]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        $data['activo'] = $data['activo'] ?? 1;
        $data['debe_cambiar_password'] = $data['debe_cambiar_password'] ?? 0;

        DB::update(
            "UPDATE usuarios
SET local_id = :local_id,
    nombre = :nombre,
    email = :email,
    telefono = :telefono,
    activo = :activo,
    debe_cambiar_password = :debe_cambiar_password,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND deleted_at IS NULL",
            $data
        );

        if (!empty($data['password_hash'])) {
            DB::update(
                "UPDATE usuarios
SET password_hash = :password_hash,
    token_version = token_version + 1,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND deleted_at IS NULL",
                ['password_hash' => $data['password_hash'], 'id' => $id]
            );
        }

        $row = DB::selectOne(
            "SELECT u.id, u.empresa_id, u.local_id, u.nombre, u.email, u.telefono, u.activo,
       u.debe_cambiar_password, u.ultimo_acceso, u.created_at, u.updated_at,
       (
         SELECT JSON_ARRAYAGG(r.codigo) FROM usuario_roles ur
         JOIN roles r ON r.id = ur.rol_id
         WHERE ur.usuario_id = u.id
       ) AS roles
FROM usuarios u
WHERE u.id = :id",
            ['id' => $id]
        );

        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $affected = DB::update(
            "UPDATE usuarios
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

    public function assignRoles(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'roles' => ['required', 'array'],
            'roles.*' => ['string'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        $user = DB::selectOne("SELECT id FROM usuarios WHERE id = :id AND deleted_at IS NULL", ['id' => $id]);
        if (!$user) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return DB::transaction(function () use ($id, $data) {
            if (empty($data['roles'])) {
                DB::delete("DELETE FROM usuario_roles WHERE usuario_id = :usuario_id", ['usuario_id' => $id]);
            } else {
                $placeholders = implode(',', array_fill(0, count($data['roles']), '?'));
                $roles = DB::select("SELECT r.id, r.codigo FROM roles r WHERE r.codigo IN ($placeholders)", $data['roles']);
                if (count($roles) !== count($data['roles'])) {
                    return response()->json([
                        'error' => 'Validation',
                        'fields' => ['roles' => ['algunos roles no existen']],
                    ], 422);
                }
                DB::delete("DELETE FROM usuario_roles WHERE usuario_id = :usuario_id", ['usuario_id' => $id]);
                foreach ($roles as $role) {
                    DB::insert(
                        "INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (:usuario_id, :rol_id)",
                        ['usuario_id' => $id, 'rol_id' => $role->id]
                    );
                }
            }

            $row = DB::selectOne(
                "SELECT
  u.id, u.nombre, u.email,
  (SELECT JSON_ARRAYAGG(r.codigo)
   FROM usuario_roles ur JOIN roles r ON r.id = ur.rol_id
   WHERE ur.usuario_id = u.id) AS roles
FROM usuarios u
WHERE u.id = :usuario_id",
                ['usuario_id' => $id]
            );
            return ['data' => (array) $row];
        });
    }
}
