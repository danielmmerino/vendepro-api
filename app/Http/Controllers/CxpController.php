<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CxpController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proveedor_id' => ['nullable', 'uuid'],
            'estado_saldo' => ['nullable', 'in:pendiente,pagada'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date'],
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
            'estado_saldo' => $request->query('estado_saldo'),
            'fini' => $request->query('fecha_desde'),
            'ffin' => $request->query('fecha_hasta'),
            'limit' => $per,
            'offset' => $off,
        ];

        $sortParam = $request->query('sort');
        $allowed = ['fecha_emision', 'fecha_vencimiento', 'total', 'saldo_pendiente', 'estado', 'created_at'];
        $orderParts = [];
        if ($sortParam) {
            foreach (explode(',', $sortParam) as $s) {
                $dir = 'ASC';
                if (str_starts_with($s, '-')) {
                    $dir = 'DESC';
                    $s = substr($s, 1);
                }
                if (in_array($s, $allowed, true)) {
                    $orderParts[] = "cxp.$s $dir";
                }
            }
        }
        if (!$orderParts) {
            $orderParts[] = 'cxp.fecha_emision DESC';
        }
        $orderBy = implode(', ', $orderParts);

        $sql = "SELECT cxp.* FROM cuentas_por_pagar cxp
JOIN proveedores p ON p.id = cxp.proveedor_id
WHERE (:proveedor_id IS NULL OR cxp.proveedor_id = :proveedor_id)
  AND (:estado_saldo IS NULL OR cxp.estado = :estado_saldo)
  AND (:fini IS NULL OR cxp.fecha_emision >= :fini)
  AND (:ffin IS NULL OR cxp.fecha_emision <= :ffin)
ORDER BY $orderBy
LIMIT :limit OFFSET :offset";
        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total FROM cuentas_por_pagar cxp
JOIN proveedores p ON p.id = cxp.proveedor_id
WHERE (:proveedor_id IS NULL OR cxp.proveedor_id = :proveedor_id)
  AND (:estado_saldo IS NULL OR cxp.estado = :estado_saldo)
  AND (:fini IS NULL OR cxp.fecha_emision >= :fini)
  AND (:ffin IS NULL OR cxp.fecha_emision <= :ffin)";
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

    public function show($id)
    {
        $row = DB::selectOne(
            "SELECT cxp.*, c.numero_factura, c.fecha, p.nombre AS proveedor
FROM cuentas_por_pagar cxp
JOIN compras c ON c.id = cxp.compra_id
JOIN proveedores p ON p.id = cxp.proveedor_id
WHERE cxp.id = :id
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
}
