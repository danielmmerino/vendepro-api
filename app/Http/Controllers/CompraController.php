<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
{
    public function aprobar(Request $request, $id)
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

        $compra = DB::selectOne(
            "SELECT id, estado FROM compras WHERE id = :id AND empresa_id = :empresa_id",
            ['id' => $id, 'empresa_id' => $empresa_id]
        );
        if (!$compra) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        if (($compra->estado ?? '') !== 'borrador') {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Estado inválido',
            ], 409);
        }

        $data = null;

        DB::transaction(function () use ($id, &$data) {
            DB::update(
                "UPDATE compras SET estado = 'aprobada', fecha_aprobacion = NOW(), updated_at = NOW() WHERE id = :id",
                ['id' => $id]
            );

            DB::insert(
                "INSERT INTO inventario_movimientos(id, fecha, producto_id, bodega_destino_id, tipo, cantidad, costo_unitario, referencia, created_at, updated_at)
SELECT gen_uuid(), NOW(), ci.producto_id, ci.bodega_id, 'entrada', ci.cantidad, ci.costo_unitario, c.numero_factura, NOW(), NOW()
FROM compra_items ci
JOIN compras c ON c.id = ci.compra_id
WHERE ci.compra_id = :compra_id",
                ['compra_id' => $id]
            );

            $items = DB::select(
                "SELECT producto_id, bodega_id, cantidad, costo_unitario FROM compra_items WHERE compra_id = :id",
                ['id' => $id]
            );

            foreach ($items as $item) {
                $saldo = DB::selectOne(
                    "SELECT cantidad, costo_promedio FROM inventario_saldos WHERE bodega_id = :bodega_id AND producto_id = :producto_id FOR UPDATE",
                    ['bodega_id' => $item->bodega_id, 'producto_id' => $item->producto_id]
                );

                if ($saldo) {
                    DB::update(
                        "UPDATE inventario_saldos
SET cantidad = cantidad + :cantidad,
    costo_promedio = ((cantidad*costo_promedio)+(:cantidad*:costo_unitario))/(cantidad+:cantidad),
    updated_at = NOW()
WHERE bodega_id = :bodega_id AND producto_id = :producto_id",
                        [
                            'cantidad' => $item->cantidad,
                            'costo_unitario' => $item->costo_unitario,
                            'bodega_id' => $item->bodega_id,
                            'producto_id' => $item->producto_id,
                        ]
                    );
                } else {
                    DB::insert(
                        "INSERT INTO inventario_saldos(id, bodega_id, producto_id, cantidad, costo_promedio, created_at, updated_at)
VALUES (gen_uuid(), :bodega_id, :producto_id, :cantidad, :costo_unitario, NOW(), NOW())",
                        [
                            'bodega_id' => $item->bodega_id,
                            'producto_id' => $item->producto_id,
                            'cantidad' => $item->cantidad,
                            'costo_unitario' => $item->costo_unitario,
                        ]
                    );
                }
            }

            $row = DB::selectOne("SELECT * FROM compras WHERE id = :id", ['id' => $id]);
            $row->items = DB::select("SELECT * FROM compra_items WHERE compra_id = :id", ['id' => $id]);
            $data = $row;
        });

        return ['data' => (array) $data];
    }
}

