<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PagoProveedorController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cxp_id' => ['required', 'uuid'],
            'fecha_pago' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'forma_pago' => ['required', 'string', 'max:50'],
            'referencia' => ['nullable', 'string', 'max:100'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();

        DB::beginTransaction();
        try {
            $cxp = DB::selectOne(
                'SELECT id, saldo_pendiente, estado FROM cuentas_por_pagar WHERE id = :id FOR UPDATE',
                ['id' => $data['cxp_id']]
            );
            if (!$cxp) {
                DB::rollBack();
                return response()->json([
                    'error' => 'NotFound',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }
            if ($cxp->estado !== 'pendiente') {
                DB::rollBack();
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'La cuenta no está pendiente',
                ], 409);
            }
            if ($data['monto'] > $cxp->saldo_pendiente) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'El monto excede el saldo pendiente',
                ], 409);
            }

            $id = (string) Str::uuid();
            DB::insert(
                'INSERT INTO pagos_proveedor(id, cxp_id, fecha_pago, monto, forma_pago, referencia, created_at, updated_at)
                VALUES (:id, :cxp_id, :fecha_pago, :monto, :forma_pago, :referencia, NOW(), NOW())',
                [
                    'id' => $id,
                    'cxp_id' => $data['cxp_id'],
                    'fecha_pago' => $data['fecha_pago'],
                    'monto' => $data['monto'],
                    'forma_pago' => $data['forma_pago'],
                    'referencia' => $data['referencia'],
                ]
            );

            DB::update(
                "UPDATE cuentas_por_pagar
SET saldo_pendiente = saldo_pendiente - :monto,
    estado = CASE WHEN saldo_pendiente - :monto <= 0 THEN 'pagada' ELSE 'pendiente' END,
    updated_at = NOW()
WHERE id = :cxp_id",
                [
                    'monto' => $data['monto'],
                    'cxp_id' => $data['cxp_id'],
                ]
            );

            $row = DB::selectOne('SELECT * FROM pagos_proveedor WHERE id = :id', ['id' => $id]);
            DB::commit();
            return response()->json(['data' => (array) $row], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function pagos($id)
    {
        $rows = DB::select(
            'SELECT * FROM pagos_proveedor WHERE cxp_id = :id ORDER BY fecha_pago DESC',
            ['id' => $id]
        );
        return ['data' => array_map(fn ($r) => (array) $r, $rows)];
    }

    public function anular($id)
    {
        $pago = DB::selectOne('SELECT * FROM pagos_proveedor WHERE id = :id', ['id' => $id]);
        if (!$pago) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        DB::transaction(function () use ($pago) {
            DB::delete('DELETE FROM pagos_proveedor WHERE id = :id', ['id' => $pago->id]);
            DB::update(
                "UPDATE cuentas_por_pagar SET saldo_pendiente = saldo_pendiente + :monto, estado = 'pendiente', updated_at = NOW() WHERE id = :id",
                ['monto' => $pago->monto, 'id' => $pago->cxp_id]
            );
        });

        return ['message' => 'Pago anulado'];
    }
}
