<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnidadMedidaController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT um.*
FROM unidades_medida um
WHERE (:empresa_id IS NULL AND um.empresa_id IS NOT NULL OR :empresa_id IS NOT NULL)
  AND (
       (:empresa_id IS NULL AND 1=1)
       OR (um.empresa_id = :empresa_id OR um.empresa_id IS NULL)
      )
  AND (:q IS NULL OR (um.nombre LIKE CONCAT('%', :q, '%') OR um.abreviatura LIKE CONCAT('%', :q, '%')))
ORDER BY um.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM unidades_medida um
WHERE (:empresa_id IS NULL AND um.empresa_id IS NOT NULL OR :empresa_id IS NOT NULL)
  AND ((:empresa_id IS NULL AND 1=1) OR (um.empresa_id = :empresa_id OR um.empresa_id IS NULL))
  AND (:q IS NULL OR (um.nombre LIKE CONCAT('%', :q, '%') OR um.abreviatura LIKE CONCAT('%', :q, '%')));";

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
            'abreviatura' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();

        $exists = DB::selectOne(
            "SELECT id FROM unidades_medida WHERE (empresa_id <=> :empresa_id) AND abreviatura = :abreviatura",
            ['empresa_id' => $data['empresa_id'] ?? null, 'abreviatura' => $data['abreviatura']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO unidades_medida (empresa_id, nombre, abreviatura, created_at, updated_at)
VALUES (:empresa_id, :nombre, :abreviatura, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [
                    'empresa_id' => $data['empresa_id'] ?? null,
                    'nombre' => $data['nombre'],
                    'abreviatura' => $data['abreviatura'],
                ]
            );
            $row = DB::selectOne("SELECT * FROM unidades_medida WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT * FROM unidades_medida WHERE id = :id LIMIT 1",
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
            'abreviatura' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $row = DB::selectOne("SELECT * FROM unidades_medida WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $data = $validator->validated();
        if ($data['abreviatura'] !== $row->abreviatura) {
            $exists = DB::selectOne(
                "SELECT id FROM unidades_medida WHERE (empresa_id <=> :empresa_id) AND abreviatura = :abreviatura AND id <> :id",
                ['empresa_id' => $row->empresa_id, 'abreviatura' => $data['abreviatura'], 'id' => $id]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        DB::update(
            "UPDATE unidades_medida
SET nombre = :nombre,
    abreviatura = :abreviatura,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id",
            [
                'nombre' => $data['nombre'],
                'abreviatura' => $data['abreviatura'],
                'id' => $id,
            ]
        );
        $row = DB::selectOne("SELECT * FROM unidades_medida WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $row = DB::selectOne("SELECT id FROM unidades_medida WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $ref = DB::selectOne("SELECT id FROM productos WHERE unidad_id = :id LIMIT 1", ['id' => $id]);
        if ($ref) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene relaciones',
            ], 409);
        }

        DB::delete("DELETE FROM unidades_medida WHERE id = :id", ['id' => $id]);
        return response()->json(null, 204);
    }
}

