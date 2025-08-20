<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'empresa_id' => ['required', 'integer'],
            'categoria_id' => ['nullable', 'integer'],
            'tipo' => ['nullable'],
            'activo' => ['nullable', 'integer'],
            'q' => ['nullable', 'string'],
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

        $params = [
            'empresa_id' => $data['empresa_id'],
            'categoria_id' => $data['categoria_id'] ?? null,
            'tipo' => $data['tipo'] ?? null,
            'activo' => $data['activo'] ?? null,
            'q' => $data['q'] ?? null,
            'per' => $per,
            'off' => $off,
        ];

        $sql = "SELECT
  p.id, p.empresa_id, p.categoria_id, p.codigo, p.nombre, p.descripcion,
  p.tipo, p.sku, p.unidad_id, p.impuesto_id, p.precio_venta, p.costo_promedio,
  p.url_imagen, p.es_receta, p.activo, p.created_at, p.updated_at,
  c.nombre AS categoria_nombre,
  um.abreviatura AS unidad,
  i.codigo AS impuesto_codigo, i.porcentaje AS impuesto_porcentaje
FROM productos p
LEFT JOIN categorias_producto c ON c.id = p.categoria_id
LEFT JOIN unidades_medida um ON um.id = p.unidad_id
LEFT JOIN impuestos i ON i.id = p.impuesto_id
WHERE p.empresa_id = :empresa_id
  AND p.deleted_at IS NULL
  AND (:categoria_id IS NULL OR p.categoria_id = :categoria_id)
  AND (:tipo IS NULL OR p.tipo = :tipo)
  AND (:activo IS NULL OR p.activo = :activo)
  AND (:q IS NULL OR (
       p.codigo LIKE CONCAT('%', :q, '%') OR
       p.nombre LIKE CONCAT('%', :q, '%') OR
       p.sku LIKE CONCAT('%', :q, '%')
  ))
ORDER BY p.id DESC
LIMIT :per OFFSET :off";

        $rows = DB::select($sql, $params);

        $countSql = "SELECT COUNT(1) AS total
FROM productos p
WHERE p.empresa_id = :empresa_id
  AND p.deleted_at IS NULL
  AND (:categoria_id IS NULL OR p.categoria_id = :categoria_id)
  AND (:tipo IS NULL OR p.tipo = :tipo)
  AND (:activo IS NULL OR p.activo = :activo)
  AND (:q IS NULL OR (
       p.codigo LIKE CONCAT('%', :q, '%') OR
       p.nombre LIKE CONCAT('%', :q, '%') OR
       p.sku LIKE CONCAT('%', :q, '%')
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            'categoria_id' => ['nullable', 'integer'],
            'codigo' => ['required'],
            'nombre' => ['required'],
            'descripcion' => ['nullable'],
            'tipo' => ['required', 'in:BIEN,SERVICIO,INSUMO,MENU'],
            'sku' => ['nullable'],
            'unidad_id' => ['nullable', 'integer'],
            'impuesto_id' => ['nullable', 'integer'],
            'precio_venta' => ['numeric', 'min:0'],
            'costo_promedio' => ['numeric', 'min:0'],
            'url_imagen' => ['nullable', 'string'],
            'es_receta' => ['boolean'],
            'activo' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();

        $exists = DB::selectOne(
            "SELECT id FROM productos WHERE empresa_id = :empresa_id AND codigo = :codigo AND deleted_at IS NULL",
            ['empresa_id' => $data['empresa_id'], 'codigo' => $data['codigo']]
        );
        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Duplicado',
            ], 409);
        }

        if (isset($data['categoria_id'])) {
            $cat = DB::selectOne(
                "SELECT id FROM categorias_producto WHERE id = :id AND empresa_id = :empresa_id",
                ['id' => $data['categoria_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$cat) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['categoria_id' => ['No existe']],
                ], 422);
            }
        }
        if (isset($data['unidad_id'])) {
            $um = DB::selectOne(
                "SELECT id FROM unidades_medida WHERE id = :id AND (empresa_id = :empresa_id OR empresa_id IS NULL)",
                ['id' => $data['unidad_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$um) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['unidad_id' => ['No existe']],
                ], 422);
            }
        }
        if (isset($data['impuesto_id'])) {
            $imp = DB::selectOne(
                "SELECT id FROM impuestos WHERE id = :id AND (empresa_id = :empresa_id OR empresa_id IS NULL)",
                ['id' => $data['impuesto_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$imp) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['impuesto_id' => ['No existe']],
                ], 422);
            }
        }

        return DB::transaction(function () use ($data) {
            DB::insert(
                "INSERT INTO productos
(empresa_id, categoria_id, codigo, nombre, descripcion, tipo, sku, unidad_id, impuesto_id,
 precio_venta, costo_promedio, url_imagen, es_receta, activo, created_at, updated_at)
VALUES
(:empresa_id, :categoria_id, :codigo, :nombre, :descripcion, :tipo, :sku, :unidad_id, :impuesto_id,
 :precio_venta, :costo_promedio, :url_imagen, :es_receta, :activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [
                    'empresa_id' => $data['empresa_id'],
                    'categoria_id' => $data['categoria_id'] ?? null,
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'tipo' => $data['tipo'],
                    'sku' => $data['sku'] ?? null,
                    'unidad_id' => $data['unidad_id'] ?? null,
                    'impuesto_id' => $data['impuesto_id'] ?? null,
                    'precio_venta' => $data['precio_venta'] ?? 0,
                    'costo_promedio' => $data['costo_promedio'] ?? 0,
                    'url_imagen' => $data['url_imagen'] ?? null,
                    'es_receta' => $data['es_receta'] ?? 0,
                    'activo' => $data['activo'] ?? 1,
                ]
            );
            $row = DB::selectOne("SELECT * FROM productos WHERE id = LAST_INSERT_ID()");
            return ['data' => (array) $row];
        });
    }

    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->query(), [
            'empresa_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = $validator->validated()['empresa_id'];
        $row = DB::selectOne(
            "SELECT
  p.*, c.nombre AS categoria_nombre, um.abreviatura AS unidad,
  i.codigo AS impuesto_codigo, i.porcentaje AS impuesto_porcentaje
FROM productos p
LEFT JOIN categorias_producto c ON c.id = p.categoria_id
LEFT JOIN unidades_medida um ON um.id = p.unidad_id
LEFT JOIN impuestos i ON i.id = p.impuesto_id
WHERE p.id = :id AND p.empresa_id = :empresa_id AND p.deleted_at IS NULL
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            'categoria_id' => ['nullable', 'integer'],
            'codigo' => ['required'],
            'nombre' => ['required'],
            'descripcion' => ['nullable'],
            'tipo' => ['required', 'in:BIEN,SERVICIO,INSUMO,MENU'],
            'sku' => ['nullable'],
            'unidad_id' => ['nullable', 'integer'],
            'impuesto_id' => ['nullable', 'integer'],
            'precio_venta' => ['numeric', 'min:0'],
            'costo_promedio' => ['numeric', 'min:0'],
            'url_imagen' => ['nullable', 'string'],
            'es_receta' => ['boolean'],
            'activo' => ['boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();

        $row = DB::selectOne(
            "SELECT * FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL",
            ['id' => $id, 'empresa_id' => $data['empresa_id']]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        if ($data['codigo'] !== $row->codigo) {
            $exists = DB::selectOne(
                "SELECT id FROM productos WHERE empresa_id = :empresa_id AND codigo = :codigo AND id <> :id AND deleted_at IS NULL",
                ['empresa_id' => $data['empresa_id'], 'codigo' => $data['codigo'], 'id' => $id]
            );
            if ($exists) {
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Duplicado',
                ], 409);
            }
        }
        if (isset($data['categoria_id'])) {
            $cat = DB::selectOne(
                "SELECT id FROM categorias_producto WHERE id = :id AND empresa_id = :empresa_id",
                ['id' => $data['categoria_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$cat) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['categoria_id' => ['No existe']],
                ], 422);
            }
        }
        if (isset($data['unidad_id'])) {
            $um = DB::selectOne(
                "SELECT id FROM unidades_medida WHERE id = :id AND (empresa_id = :empresa_id OR empresa_id IS NULL)",
                ['id' => $data['unidad_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$um) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['unidad_id' => ['No existe']],
                ], 422);
            }
        }
        if (isset($data['impuesto_id'])) {
            $imp = DB::selectOne(
                "SELECT id FROM impuestos WHERE id = :id AND (empresa_id = :empresa_id OR empresa_id IS NULL)",
                ['id' => $data['impuesto_id'], 'empresa_id' => $data['empresa_id']]
            );
            if (!$imp) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['impuesto_id' => ['No existe']],
                ], 422);
            }
        }

        DB::update(
            "UPDATE productos
SET categoria_id   = :categoria_id,
    codigo         = :codigo,
    nombre         = :nombre,
    descripcion    = :descripcion,
    tipo           = :tipo,
    sku            = :sku,
    unidad_id      = :unidad_id,
    impuesto_id    = :impuesto_id,
    precio_venta   = :precio_venta,
    costo_promedio = :costo_promedio,
    url_imagen     = :url_imagen,
    es_receta      = :es_receta,
    activo         = :activo,
    updated_at     = CURRENT_TIMESTAMP
WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL",
            [
                'categoria_id' => $data['categoria_id'] ?? null,
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'tipo' => $data['tipo'],
                'sku' => $data['sku'] ?? null,
                'unidad_id' => $data['unidad_id'] ?? null,
                'impuesto_id' => $data['impuesto_id'] ?? null,
                'precio_venta' => $data['precio_venta'] ?? 0,
                'costo_promedio' => $data['costo_promedio'] ?? 0,
                'url_imagen' => $data['url_imagen'] ?? null,
                'es_receta' => $data['es_receta'] ?? 0,
                'activo' => $data['activo'] ?? 1,
                'id' => $id,
                'empresa_id' => $data['empresa_id'],
            ]
        );

        $row = DB::selectOne("SELECT * FROM productos WHERE id = :id AND empresa_id = :empresa_id", ['id' => $id, 'empresa_id' => $data['empresa_id']]);
        return ['data' => (array) $row];
    }

    public function destroy(Request $request, $id)
    {
        $validator = Validator::make($request->query(), [
            'empresa_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = $validator->validated()['empresa_id'];
        $row = DB::selectOne(
            "SELECT id FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        if (!$row) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $ref = DB::selectOne(
            "SELECT
  (SELECT COUNT(1) FROM recetas r WHERE r.producto_id = :id OR r.insumo_id = :id) AS cnt_rec,
  (SELECT COUNT(1) FROM pedidos_det pd WHERE pd.producto_id = :id) AS cnt_ped,
  (SELECT COUNT(1) FROM facturas_det fd WHERE fd.producto_id = :id) AS cnt_fac,
  (SELECT COUNT(1) FROM compras_det cd  WHERE cd.producto_id = :id) AS cnt_comp,
  (SELECT COUNT(1) FROM stock s WHERE s.producto_id = :id) AS cnt_stock",
            ['id' => $id]
        );
        if ($ref->cnt_rec > 0 || $ref->cnt_ped > 0 || $ref->cnt_fac > 0 || $ref->cnt_comp > 0 || $ref->cnt_stock > 0) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Tiene referencias',
            ], 409);
        }

        DB::update(
            "UPDATE productos
SET deleted_at = CURRENT_TIMESTAMP,
    updated_at = CURRENT_TIMESTAMP
WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );

        return response()->json(null, 204);
    }
}
