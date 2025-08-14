<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertCuentaItemsRequest;
use App\Http\Resources\CuentaResource;
use App\Models\Cuenta;
use App\Models\CuentaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CuentaItemController extends Controller
{
    /**
     * @OA\Post(
     *   path="/v1/cuentas/{id}/items",
     *   summary="Asignar items a la cuenta",
     *   @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *       @OA\Property(property="items", type="array", @OA\Items(
     *           @OA\Property(property="pedido_item_id", type="string", format="uuid"),
     *           @OA\Property(property="cantidad", type="number", example=1.5),
     *           @OA\Property(property="monto", type="number", example=10.00)
     *       ))
     *   )),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function store(UpsertCuentaItemsRequest $request, string $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $cuenta = Cuenta::lockForUpdate()->findOrFail($id);
            if ($cuenta->estado !== 'abierta') {
                return response()->json([
                    'error' => 'Validation',
                    'message' => 'Cuenta no abierta',
                ], 422);
            }
            $pedidoId = $cuenta->pedido_id;
            foreach ($request->input('items') as $entry) {
                $pi = DB::table('pedido_items')->where('id', $entry['pedido_item_id'])->first();
                if (!$pi || $pi->pedido_id !== $pedidoId) {
                    return response()->json([
                        'error' => 'Validation',
                        'fields' => ['pedido_item_id' => ['invalid']],
                    ], 422);
                }
                $assigned = DB::table('cuenta_items as ci')
                    ->join('cuentas as c', 'c.id', '=', 'ci.cuenta_id')
                    ->where('ci.pedido_item_id', $pi->id)
                    ->where('c.pedido_id', $pedidoId)
                    ->where('c.estado', '<>', 'anulada')
                    ->where('ci.cuenta_id', '<>', $cuenta->id)
                    ->selectRaw('COALESCE(SUM(ci.cantidad),0) as cant, COALESCE(SUM(ci.monto),0) as monto')
                    ->first();
                $cantRem = $pi->cantidad - $assigned->cant;
                $montoRem = ($pi->cantidad * $pi->precio_unit) - $assigned->monto;
                $cantidad = $entry['cantidad'] ?? null;
                $monto = $entry['monto'] ?? null;
                if (is_null($cantidad) && !is_null($monto)) {
                    $cantidad = round($monto / $pi->precio_unit, 4);
                }
                if (is_null($monto) && !is_null($cantidad)) {
                    $monto = round($cantidad * $pi->precio_unit, 2);
                }
                if ($cantidad > $cantRem + 0.0001 || $monto > $montoRem + 0.01) {
                    return response()->json([
                        'error' => 'Validation',
                        'fields' => ['items' => ['remanente']]
                    ], 422);
                }
                $impuesto = round($monto * ($pi->impuesto_porcentaje / 100), 2);
                CuentaItem::updateOrCreate(
                    ['cuenta_id' => $cuenta->id, 'pedido_item_id' => $pi->id],
                    [
                        'cantidad' => $cantidad,
                        'monto' => $monto,
                        'impuesto_monto' => $impuesto,
                        'notas' => $entry['notas'] ?? null,
                    ]
                );
            }
            $totals = $cuenta->items()
                ->selectRaw('COALESCE(SUM(monto),0) as subtotal, COALESCE(SUM(impuesto_monto),0) as impuesto')
                ->first();
            $cuenta->subtotal = $totals->subtotal;
            $cuenta->impuesto = $totals->impuesto;
            $cuenta->total = round($cuenta->subtotal - $cuenta->descuento + $cuenta->impuesto, 2);
            $cuenta->save();
            $cuenta->load('items');
            return response()->json(['data' => new CuentaResource($cuenta)]);
        });
    }
}
