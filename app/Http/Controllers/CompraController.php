<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\UpdateCompraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proveedor_id' => ['nullable', 'uuid'],
            'estado' => ['nullable', 'in:borrador,aprobada,anulada'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date'],
            'q' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
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

        $params = [
            'proveedor_id' => $request->query('proveedor_id'),
            'estado' => $request->query('estado'),
            'fini' => $request->query('fecha_desde'),
            'ffin' => $request->query('fecha_hasta'),
            'q' => $request->query('q'),
            'limit' => $per,
            'offset' => $off,
        ];

        $sortParam = $request->query('sort');
        $allowed = ['fecha', 'numero_factura', 'subtotal', 'descuento', 'impuesto', 'total', 'estado', 'created_at'];
        $orderParts = [];
        if ($sortParam) {
            foreach (explode(',', $sortParam) as $s) {
                $dir = 'ASC';
                if (str_starts_with($s, '-')) {
                    $dir = 'DESC';
                    $s = substr($s, 1);
                }
                if (in_array($s, $allowed, true)) {
                    $orderParts[] = "c.$s $dir";
                }
            }
        }
        if (!$orderParts) {
            $orderParts[] = 'c.fecha DESC';
        }
        $orderBy = implode(', ', $orderParts);

        $sql = "SELECT c.* FROM compras c
WHERE (:proveedor_id IS NULL OR c.proveedor_id = :proveedor_id)
AND (:estado IS NULL OR c.estado = :estado)
AND (:fini IS NULL OR c.fecha >= :fini)
AND (:ffin IS NULL OR c.fecha <= :ffin)
AND (:q IS NULL OR c.numero_factura LIKE CONCAT('%', :q, '%'))
ORDER BY $orderBy
LIMIT :limit OFFSET :offset";
        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total FROM compras c
WHERE (:proveedor_id IS NULL OR c.proveedor_id = :proveedor_id)
AND (:estado IS NULL OR c.estado = :estado)
AND (:fini IS NULL OR c.fecha >= :fini)
AND (:ffin IS NULL OR c.fecha <= :ffin)
AND (:q IS NULL OR c.numero_factura LIKE CONCAT('%', :q, '%'))";
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

    public function store(StoreCompraRequest $request)
    {
        $data = $request->validated();

        $exists = DB::selectOne(
            "SELECT id FROM compras WHERE proveedor_id = :proveedor_id AND numero_factura = :numero_factura",
            ['proveedor_id' => $data['proveedor_id'], 'numero_factura' => $data['numero_factura']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        $id = (string) Str::uuid();
        DB::insert(
            "INSERT INTO compras (id, proveedor_id, fecha, numero_factura, subtotal, descuento, impuesto, total, estado, observacion, usuario_id, created_at, updated_at)
VALUES (:id, :proveedor_id, :fecha, :numero_factura, :subtotal, :descuento, :impuesto, :total, :estado, :observacion, :usuario_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            [
                'id' => $id,
                'proveedor_id' => $data['proveedor_id'],
                'fecha' => $data['fecha'],
                'numero_factura' => $data['numero_factura'],
                'subtotal' => $data['subtotal'],
                'descuento' => $data['descuento'] ?? 0,
                'impuesto' => $data['impuesto'] ?? 0,
                'total' => $data['total'],
                'estado' => $data['estado'] ?? 'borrador',
                'observacion' => $data['observacion'] ?? null,
                'usuario_id' => $data['usuario_id'] ?? null,
            ]
        );
        $row = DB::selectOne("SELECT * FROM compras WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function show($id)
    {
        $row = DB::selectOne("SELECT * FROM compras WHERE id = :id LIMIT 1", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return ['data' => (array) $row];
    }

    public function update(UpdateCompraRequest $request, $id)
    {
        $data = $request->validated();
        $row = DB::selectOne("SELECT * FROM compras WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        if ($row->estado !== 'borrador') {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Solo se puede modificar en estado borrador',
            ], 409);
        }
        if ($data['numero_factura'] !== $row->numero_factura || $data['proveedor_id'] !== $row->proveedor_id) {
            $exists = DB::selectOne(
                "SELECT id FROM compras WHERE proveedor_id = :proveedor_id AND numero_factura = :numero_factura AND id <> :id",
                [
                    'proveedor_id' => $data['proveedor_id'],
                    'numero_factura' => $data['numero_factura'],
                    'id' => $id,
                ]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }

        DB::update(
            "UPDATE compras SET proveedor_id = :proveedor_id, fecha = :fecha, numero_factura = :numero_factura, subtotal = :subtotal, descuento = :descuento, impuesto = :impuesto, total = :total, estado = :estado, observacion = :observacion, usuario_id = :usuario_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            [
                'proveedor_id' => $data['proveedor_id'],
                'fecha' => $data['fecha'],
                'numero_factura' => $data['numero_factura'],
                'subtotal' => $data['subtotal'],
                'descuento' => $data['descuento'] ?? 0,
                'impuesto' => $data['impuesto'] ?? 0,
                'total' => $data['total'],
                'estado' => $data['estado'],
                'observacion' => $data['observacion'] ?? null,
                'usuario_id' => $data['usuario_id'] ?? null,
                'id' => $id,
            ]
        );
        $row = DB::selectOne("SELECT * FROM compras WHERE id = :id", ['id' => $id]);
        return ['data' => (array) $row];
    }

    public function destroy($id)
    {
        $row = DB::selectOne("SELECT estado FROM compras WHERE id = :id", ['id' => $id]);
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        if ($row->estado !== 'borrador') {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Solo se puede eliminar en estado borrador',
            ], 409);
        }
        DB::update(
            "UPDATE compras SET deleted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['id' => $id]
        );
        return response()->json(null, 204);
    }
}
