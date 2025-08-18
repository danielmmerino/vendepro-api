<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\CajaMovimiento;
use App\Models\CajaCierre;
use App\Http\Requests\StoreCajaCierreRequest;
use Illuminate\Support\Facades\DB;

class CajaCierreController extends Controller
{
    public function store(StoreCajaCierreRequest $request)
    {
        $data = $request->validated();
        $apertura = CajaApertura::find($data['apertura_id']);
        if (!$apertura || $apertura->estado !== 'abierta') {
            return response()->json([
                'error' => ['code' => 'CONFLICT','message' => 'Apertura no disponible']
            ],409);
        }
        $ingresos = CajaMovimiento::where('apertura_id',$apertura->id)
            ->whereIn('tipo',['ingreso','venta','propina'])
            ->sum('monto');
        $egresos = CajaMovimiento::where('apertura_id',$apertura->id)
            ->whereIn('tipo',['egreso','cambio','deposito'])
            ->sum('monto');
        $esperado = $apertura->saldo_inicial + $ingresos - $egresos;
        $contado = $data['efectivo_total_contado'];
        $cierre = CajaCierre::create([
            'apertura_id' => $apertura->id,
            'esperado_efectivo' => $esperado,
            'contado_efectivo' => $contado,
            'diferencia' => round($contado - $esperado,2),
            'detalle_conteo' => $data['conteo_efectivo'] ?? [],
            'observacion' => $data['observacion'] ?? null,
        ]);
        $apertura->estado = 'cerrada';
        $apertura->cerrado_at = now();
        $apertura->save();
        return ['data' => $cierre];
    }
}
