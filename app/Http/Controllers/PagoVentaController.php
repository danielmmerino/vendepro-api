<?php

namespace App\Http\Controllers;

use App\Models\PagoVenta;
use App\Models\PagoVentaDetalle;
use App\Models\CajaApertura;
use App\Models\CajaMovimiento;
use App\Http\Requests\StorePagoVentaRequest;
use App\Http\Requests\AnularPagoVentaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoVentaController extends Controller
{
    public function store(StorePagoVentaRequest $request)
    {
        $data = $request->validated();
        $idempotency = $data['idempotency_key'] ?? $request->header('Idempotency-Key');
        if ($idempotency) {
            $existing = PagoVenta::where('factura_id',$data['factura_id'])
                ->where('idempotency_key',$idempotency)
                ->first();
            if ($existing) {
                return ['data' => $existing];
            }
        }

        $items = $data['items_pago'];
        $total = collect($items)->sum('monto') + ($data['propina'] ?? 0) + ($data['redondeo'] ?? 0);

        // efectivo requires apertura
        $requiresCaja = collect($items)->contains(fn($i) => $i['metodo'] === 'efectivo');
        if ($requiresCaja) {
            $aperturaId = $data['caja']['apertura_id'] ?? null;
            $apertura = CajaApertura::find($aperturaId);
            if (!$apertura || $apertura->estado !== 'abierta') {
                return response()->json([
                    'error' => ['code' => 'CONFLICT','message' => 'Caja no abierta']
                ],409);
            }
        }

        return DB::transaction(function() use ($data,$items,$total,$idempotency,$requiresCaja) {
            $pago = PagoVenta::create([
                'factura_id' => $data['factura_id'],
                'total' => $total,
                'propina' => $data['propina'] ?? 0,
                'redondeo' => $data['redondeo'] ?? 0,
                'apertura_id' => $data['caja']['apertura_id'] ?? null,
                'idempotency_key' => $idempotency,
            ]);
            foreach ($items as $i) {
                PagoVentaDetalle::create([
                    'pago_id' => $pago->id,
                    'metodo' => $i['metodo'],
                    'monto' => $i['monto'],
                    'detalle' => $i['tarjeta'] ?? $i['transferencia'] ?? null,
                ]);
                if ($i['metodo'] === 'efectivo' && $requiresCaja) {
                    CajaMovimiento::create([
                        'apertura_id' => $data['caja']['apertura_id'],
                        'tipo' => 'venta',
                        'monto' => $i['monto'],
                        'motivo' => 'Pago factura',
                    ]);
                }
            }
            if (($data['propina'] ?? 0) > 0 && $requiresCaja) {
                CajaMovimiento::create([
                    'apertura_id' => $data['caja']['apertura_id'],
                    'tipo' => 'propina',
                    'monto' => $data['propina'],
                    'motivo' => 'Propina',
                ]);
            }
            return response()->json(['data' => $pago],201);
        });
    }

    public function index(Request $request)
    {
        $query = PagoVenta::query();
        if ($factura = $request->query('factura_id')) {
            $query->where('factura_id',$factura);
        }
        if ($metodo = $request->query('metodo')) {
            $query->whereHas('detalles', fn($q) => $q->where('metodo',$metodo));
        }
        if ($desde = $request->query('desde')) {
            $query->whereDate('created_at','>=',$desde);
        }
        if ($hasta = $request->query('hasta')) {
            $query->whereDate('created_at','<=',$hasta);
        }
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $rows = $query->paginate($per,['*'],'page',$page);
        return [
            'data' => $rows->items(),
            'meta' => [
                'page' => $rows->currentPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ]
        ];
    }

    public function show($id)
    {
        $pago = PagoVenta::with('detalles')->find($id);
        if (!$pago) {
            return response()->json(['error' => ['code'=>'NOT_FOUND','message'=>'No encontrado']],404);
        }
        return ['data' => $pago];
    }

    public function anular($id, AnularPagoVentaRequest $request)
    {
        $pago = PagoVenta::with('detalles')->find($id);
        if (!$pago || $pago->estado !== 'vigente') {
            return response()->json(['error' => ['code'=>'CONFLICT','message'=>'No se puede anular']],409);
        }
        $pago->estado = 'anulado';
        $pago->save();
        foreach ($pago->detalles as $d) {
            if ($d->metodo === 'efectivo' && $pago->apertura_id) {
                CajaMovimiento::create([
                    'apertura_id' => $pago->apertura_id,
                    'tipo' => 'ajuste',
                    'monto' => -1 * $d->monto,
                    'motivo' => 'Anulación pago',
                ]);
            }
        }
        return ['data' => $pago];
    }
}
