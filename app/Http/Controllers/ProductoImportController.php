<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoImportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            'file' => ['required', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = $validator->validated()['empresa_id'];
        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['file' => ['No se pudo leer']],
            ], 422);
        }
        $header = fgetcsv($handle);
        $columns = array_map(fn($h) => strtolower(trim($h)), $header);

        $inserted = 0;
        $updated = 0;
        $errors = [];
        $seen = [];
        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = [];
            foreach ($columns as $i => $col) {
                $data[$col] = trim($row[$i] ?? '');
            }
            $codigo = strtoupper($data['codigo'] ?? '');
            $nombre = $data['nombre'] ?? '';
            if ($codigo === '' || $nombre === '') {
                $errors[] = ['row' => $rowNum, 'msg' => 'codigo/nombre requerido'];
                continue;
            }
            if (isset($seen[$codigo])) {
                $errors[] = ['row' => $rowNum, 'msg' => 'codigo duplicado en archivo'];
                continue;
            }
            $seen[$codigo] = true;
            $tipo = $data['tipo'] ? strtoupper($data['tipo']) : 'BIEN';
            $precio = is_numeric($data['precio_venta'] ?? null) ? (float)$data['precio_venta'] : 0;
            $activo = ($data['activo'] ?? '1') != '0' ? 1 : 0;
            $sku = $data['sku'] ?? null;
            $descripcion = $data['descripcion'] ?? null;
            $categoria_nombre = $data['categoria_nombre'] ?? null;
            $unidad_abrev = isset($data['unidad_abrev']) ? strtoupper($data['unidad_abrev']) : null;
            $impuesto_codigo = isset($data['impuesto_codigo']) ? strtoupper($data['impuesto_codigo']) : null;

            $categoria_id = null;
            if ($categoria_nombre) {
                $categoria = DB::selectOne("SELECT id FROM categorias_producto WHERE empresa_id = :empresa_id AND nombre = :nombre", ['empresa_id' => $empresa_id, 'nombre' => $categoria_nombre]);
                if (!$categoria) {
                    DB::insert("INSERT INTO categorias_producto (empresa_id, nombre, created_at, updated_at) VALUES (:empresa_id,:nombre,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)", ['empresa_id'=>$empresa_id,'nombre'=>$categoria_nombre]);
                    $categoria_id = DB::selectOne("SELECT LAST_INSERT_ID() AS id")->id;
                } else {
                    $categoria_id = $categoria->id;
                }
            }
            $unidad_id = null;
            if ($unidad_abrev) {
                $um = DB::selectOne("SELECT id FROM unidades_medida WHERE abreviatura = :abrev AND (empresa_id = :empresa_id OR empresa_id IS NULL)", ['abrev'=>$unidad_abrev,'empresa_id'=>$empresa_id]);
                $unidad_id = $um->id ?? null;
            }
            $impuesto_id = null;
            if ($impuesto_codigo) {
                $imp = DB::selectOne("SELECT id FROM impuestos WHERE (empresa_id = :empresa_id OR empresa_id IS NULL) AND codigo = :codigo ORDER BY empresa_id DESC LIMIT 1", ['empresa_id'=>$empresa_id,'codigo'=>$impuesto_codigo]);
                $impuesto_id = $imp->id ?? null;
            }

            $exists = DB::selectOne("SELECT id FROM productos WHERE empresa_id = :empresa_id AND codigo = :codigo", ['empresa_id'=>$empresa_id,'codigo'=>$codigo]);
            DB::insert("INSERT INTO productos (empresa_id, categoria_id, codigo, nombre, descripcion, tipo, sku, unidad_id, impuesto_id, precio_venta, costo_promedio, es_receta, activo, created_at, updated_at)
VALUES (:empresa_id, :categoria_id, :codigo, :nombre, :descripcion, :tipo, :sku, :unidad_id, :impuesto_id, :precio_venta, 0, 0, :activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE
  categoria_id=VALUES(categoria_id), nombre=VALUES(nombre), descripcion=VALUES(descripcion), tipo=VALUES(tipo), sku=VALUES(sku), unidad_id=VALUES(unidad_id), impuesto_id=VALUES(impuesto_id), precio_venta=VALUES(precio_venta), costo_promedio=VALUES(costo_promedio), es_receta=VALUES(es_receta), activo=VALUES(activo), updated_at=CURRENT_TIMESTAMP", [
                'empresa_id'=>$empresa_id,
                'categoria_id'=>$categoria_id,
                'codigo'=>$codigo,
                'nombre'=>$nombre,
                'descripcion'=>$descripcion,
                'tipo'=>$tipo,
                'sku'=>$sku,
                'unidad_id'=>$unidad_id,
                'impuesto_id'=>$impuesto_id,
                'precio_venta'=>$precio,
                'activo'=>$activo,
            ]);
            if ($exists) {
                $updated++;
            } else {
                $inserted++;
            }
        }
        fclose($handle);

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }
}
