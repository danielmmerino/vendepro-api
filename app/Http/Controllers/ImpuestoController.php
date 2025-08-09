<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImpuestoController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $per = (int) $request->query('per_page', 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;

        $empresa_id = $request->query('empresa_id');
        $tipo = $request->query('tipo');
        $vigente = $request->query('vigente');
        $q = $request->query('q');

        $params = [
            'empresa_id' => $empresa_id,
            'tipo' => $tipo,
            'vigente' => $vigente,
            'q' => $q,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT i.*
FROM impuestos i
WHERE ((:empresa_id IS NULL AND i.empresa_id IS NULL) OR (i.empresa_id = :empresa_id OR i.empresa_id IS NULL))
  AND (:tipo IS NULL OR i.tipo = :tipo)
  AND (:vigente IS NULL OR i.vigente = :vigente)
  AND (:q IS NULL OR (i.codigo LIKE CONCAT('%', :q, '%') OR i.nombre LIKE CONCAT('%', :q, '%')))
ORDER BY i.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM impuestos i
WHERE ((:empresa_id IS NULL AND i.empresa_id IS NULL) OR (i.empresa_id = :empresa_id OR i.empresa_id IS NULL))
  AND (:tipo IS NULL OR i.tipo = :tipo)
  AND (:vigente IS NULL OR i.vigente = :vigente)
  AND (:q IS NULL OR (i.codigo LIKE CONCAT('%', :q, '%') OR i.nombre LIKE CONCAT('%', :q, '%')))";

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
            'codigo' => ['required'],
            'nombre' => ['required'],
            'tipo' => ['required', 'in:IVA,ICE,IRBPNR,OTRO'],
            'porcentaje' => ['required', 'numeric', 'min:0', 'max:1000'],
            'vigente' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $data['vigente'] = $data['vigente'] ?? 1;

        $exists = DB::selectOne(
            "SELECT id FROM impuestos WHERE ((empresa_id IS NULL AND :empresa_id IS NULL) OR empresa_id = :empresa_id) AND codigo = :codigo",
            ['empresa_id' => $data['empresa_id'] ?? null, 'codigo' => $data['codigo']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO impuestos (empresa_id, codigo, nombre, tipo, porcentaje, vigente, created_at, updated_at)
VALUES (:empresa_id, :codigo, :nombre, :tipo, :porcentaje, :vigente, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [
                    'empresa_id' => $data['empresa_id'] ?? null,
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'tipo' => $data['tipo'],
                    'porcentaje' => $data['porcentaje'],
                    'vigente' => $data['vigente'],
                ]
            );
            $row = DB::selectOne("SELECT * FROM impuestos WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show($id)
    {
        $row = DB::selectOne("SELECT * FROM impuestos WHERE id = :id LIMIT 1", ['id' => $id]);
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
            'tipo' => ['required', 'in:IVA,ICE,IRBPNR,OTRO'],
            'porcentaje' => ['required', 'numeric', 'min:0', 'max:1000'],
            'vigente' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $row = DB::selectOne("SELECT * FROM impuestos WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $data = $validator->validated();
        $data['vigente'] = $data['vigente'] ?? 1;

        if ($data['codigo'] !== $row->codigo) {
            $exists = DB::selectOne(
                "SELECT id FROM impuestos WHERE ((empresa_id IS NULL AND :empresa_id IS NULL) OR empresa_id = :empresa_id) AND codigo = :codigo AND id <> :id",
                ['empresa_id' => $row->empresa_id, 'codigo' => $data['codigo'], 'id' => $id]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        DB::update(
            "UPDATE impuestos
SET codigo = :codigo,
    nombre = :nombre,
    tipo = :tipo,
    porcentaje = :porcentaje,
    vigente = :vigente,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id",
            [
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'tipo' => $data['tipo'],
                'porcentaje' => $data['porcentaje'],
                'vigente' => $data['vigente'],
                'id' => $id,
            ]
        );
        $row = DB::selectOne("SELECT * FROM impuestos WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $row = DB::selectOne("SELECT id FROM impuestos WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $ref1 = DB::selectOne("SELECT id FROM productos WHERE impuesto_id = :id LIMIT 1", ['id' => $id]);
        $ref2 = DB::selectOne("SELECT id FROM facturas WHERE impuesto_id = :id LIMIT 1", ['id' => $id]);
        if ($ref1 || $ref2) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene relaciones',
            ], 409);
        }

        DB::delete("DELETE FROM impuestos WHERE id = :id", ['id' => $id]);
        return response()->json(null, 204);
    }
}

