<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'local_id' => ['required', 'integer'],
            'categoria_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string'],
            'solo_con_stock' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $page = max((int) ($data['page'] ?? 1), 1);
        $per = (int) ($data['per_page'] ?? 20);
        $per = $per > 100 ? 100 : $per;
        $off = ($page - 1) * $per;
        $solo = (int) ($data['solo_con_stock'] ?? 0);

        $cacheKey = 'menu:' . md5(json_encode([$data['local_id'], $data['categoria_id'] ?? null, $data['q'] ?? null, $solo, $page, $per]));
        return Cache::remember($cacheKey, 60, function () use ($data, $per, $off, $page, $solo) {
            $params = [
                'local_id' => $data['local_id'],
                'categoria_id' => $data['categoria_id'] ?? null,
                'q' => $data['q'] ?? null,
                'solo_con_stock' => $solo,
                'per' => $per,
                'off' => $off,
            ];
            $sql = "SELECT
  p.id, p.codigo, p.nombre, p.descripcion, p.tipo,
  p.precio_venta, p.impuesto_id,
  c.nombre AS categoria_nombre,
  i.codigo AS impuesto_codigo, i.porcentaje AS impuesto_porcentaje,
  COALESCE((
    SELECT SUM(s.cantidad)
    FROM bodegas b
    JOIN stock s ON s.bodega_id = b.id
    WHERE b.local_id = :local_id AND s.producto_id = p.id
  ), 0) AS stock_local
FROM productos p
LEFT JOIN categorias_producto c ON c.id = p.categoria_id
LEFT JOIN impuestos i ON i.id = p.impuesto_id
JOIN locales l ON l.id = :local_id
WHERE p.empresa_id = l.empresa_id
  AND p.deleted_at IS NULL
  AND p.activo = 1
  AND p.tipo IN ('BIEN','SERVICIO','MENU')
  AND (:categoria_id IS NULL OR p.categoria_id = :categoria_id)
  AND (:q IS NULL OR (p.codigo LIKE CONCAT('%', :q, '%') OR p.nombre LIKE CONCAT('%', :q, '%')))
  AND (:solo_con_stock = 0 OR COALESCE((
       SELECT SUM(s2.cantidad)
       FROM bodegas b2
       JOIN stock s2 ON s2.bodega_id = b2.id
       WHERE b2.local_id = :local_id AND s2.producto_id = p.id
  ), 0) > 0)
ORDER BY COALESCE(c.orden, 9999) ASC, p.nombre ASC
LIMIT :per OFFSET :off";
            $rows = DB::select($sql, $params);

            $countSql = "SELECT COUNT(1) AS total
FROM productos p
JOIN locales l ON l.id = :local_id
WHERE p.empresa_id = l.empresa_id
  AND p.deleted_at IS NULL
  AND p.activo = 1
  AND p.tipo IN ('BIEN','SERVICIO','MENU')
  AND (:categoria_id IS NULL OR p.categoria_id = :categoria_id)
  AND (:q IS NULL OR (p.codigo LIKE CONCAT('%', :q, '%') OR p.nombre LIKE CONCAT('%', :q, '%')))
  AND (:solo_con_stock = 0 OR COALESCE((
       SELECT SUM(s2.cantidad)
       FROM bodegas b2
       JOIN stock s2 ON s2.bodega_id = b2.id
       WHERE b2.local_id = :local_id AND s2.producto_id = p.id
  ), 0) > 0)";
            $total = DB::selectOne($countSql, $params)->total ?? 0;

            return [
                'data' => array_map(fn($r) => (array) $r, $rows),
                'pagination' => [
                    'page' => $page,
                    'per_page' => $per,
                    'total' => (int) $total,
                ],
            ];
        });
    }
}
