<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecetaController extends Controller
{
    public function index(Request $request, $producto_id)
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
        $prod = DB::selectOne(
            "SELECT id FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL",
            ['id' => $producto_id, 'empresa_id' => $empresa_id]
        );
        if (!$prod) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        $rows = DB::select(
            "SELECT
  r.insumo_id, p.codigo AS insumo_codigo, p.nombre AS insumo_nombre,
  r.cantidad, r.merma_porcentaje, p.unidad_id, um.abreviatura AS unidad
FROM recetas r
JOIN productos p ON p.id = r.insumo_id
LEFT JOIN unidades_medida um ON um.id = p.unidad_id
WHERE r.producto_id = :producto_id
ORDER BY p.nombre ASC",
            ['producto_id' => $producto_id]
        );
        return ['data' => array_map(fn($r) => (array) $r, $rows)];
    }

    public function store(Request $request, $producto_id)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => ['required', 'integer'],
            '*.insumo_id' => ['required', 'integer'],
            '*.cantidad' => ['required', 'numeric'],
            '*.merma_porcentaje' => ['nullable', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $empresa_id = $validator->validated()['empresa_id'];
        $lines = $request->all();
        unset($lines['empresa_id']);

        $prod = DB::selectOne("SELECT id, es_receta FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL", ['id'=>$producto_id,'empresa_id'=>$empresa_id]);
        if (!$prod) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        foreach ($lines as $idx => $line) {
            if ($line['insumo_id'] == $producto_id) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ["$idx.insumo_id" => ['No puede ser el mismo producto']],
                ], 422);
            }
            $ins = DB::selectOne("SELECT id FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL", ['id'=>$line['insumo_id'],'empresa_id'=>$empresa_id]);
            if (!$ins) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ["$idx.insumo_id" => ['No existe']],
                ], 422);
            }
        }

        return DB::transaction(function () use ($lines, $producto_id) {
            DB::delete("DELETE FROM recetas WHERE producto_id = :producto_id", ['producto_id' => $producto_id]);
            foreach ($lines as $line) {
                DB::insert(
                    "INSERT INTO recetas (producto_id, insumo_id, cantidad, merma_porcentaje)
VALUES (:producto_id, :insumo_id, :cantidad, :merma)",
                    [
                        'producto_id' => $producto_id,
                        'insumo_id' => $line['insumo_id'],
                        'cantidad' => $line['cantidad'],
                        'merma' => $line['merma_porcentaje'] ?? 0,
                    ]
                );
            }
            $rows = DB::select(
                "SELECT
  r.insumo_id, p.codigo AS insumo_codigo, p.nombre AS insumo_nombre,
  r.cantidad, r.merma_porcentaje, p.unidad_id, um.abreviatura AS unidad
FROM recetas r
JOIN productos p ON p.id = r.insumo_id
LEFT JOIN unidades_medida um ON um.id = p.unidad_id
WHERE r.producto_id = :producto_id
ORDER BY p.nombre ASC",
                ['producto_id' => $producto_id]
            );
            return ['data' => array_map(fn($r) => (array) $r, $rows)];
        });
    }

    public function destroy(Request $request, $producto_id, $insumo_id)
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
        $prod = DB::selectOne("SELECT id FROM productos WHERE id = :id AND empresa_id = :empresa_id AND deleted_at IS NULL", ['id'=>$producto_id,'empresa_id'=>$empresa_id]);
        if (!$prod) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        DB::delete("DELETE FROM recetas WHERE producto_id = :producto_id AND insumo_id = :insumo_id", ['producto_id'=>$producto_id,'insumo_id'=>$insumo_id]);
        $rows = DB::select(
            "SELECT r.insumo_id, p.codigo AS insumo_codigo, p.nombre AS insumo_nombre,
       r.cantidad, r.merma_porcentaje
FROM recetas r
JOIN productos p ON p.id = r.insumo_id
WHERE r.producto_id = :producto_id
ORDER BY p.nombre ASC",
            ['producto_id' => $producto_id]
        );
        return ['data' => array_map(fn($r) => (array) $r, $rows)];
    }
}
