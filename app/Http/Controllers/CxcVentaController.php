<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Http\Resources\CxcDocumentoResource;
use App\Http\Resources\PagoResource;
use App\Models\CxcDocumento;
use App\Models\CxcPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CxcVentaController extends Controller
{
    public function index(Request $request)
    {
        $query = CxcDocumento::query();
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id',$request->query('cliente_id'));
        }
        if ($request->filled('estado')) {
            $query->where('estado',$request->query('estado'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_emision','>=',$request->query('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_emision','<=',$request->query('hasta'));
        }
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $paginator = $query->orderBy('fecha_emision','desc')->paginate($per,['*'],'page',$page);
        return [
            'data' => CxcDocumentoResource::collection($paginator->items()),
            'meta' => [
                'current_page'=>$paginator->currentPage(),
                'per_page'=>$paginator->perPage(),
                'total'=>$paginator->total(),
            ],
            'links'=>[
                'next'=>$paginator->nextPageUrl(),
                'prev'=>$paginator->previousPageUrl(),
            ],
        ];
    }

    public function show($id)
    {
        $doc = CxcDocumento::with('pagos')->find($id);
        if(!$doc){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=> new CxcDocumentoResource($doc)];
    }

    public function storePago(StorePagoRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function() use ($data){
            $doc = CxcDocumento::lockForUpdate()->find($data['cxc_id']);
            if(!$doc){
                return response()->json(['error'=>'NotFound','message'=>'CxC no encontrada'],404);
            }
            if($data['monto'] > $doc->saldo_pendiente){
                return response()->json(['error'=>'Unprocessable','message'=>'Monto mayor al saldo'],422);
            }
            $pago = CxcPago::create($data);
            $doc->saldo_pendiente = round($doc->saldo_pendiente - $data['monto'],2);
            if($doc->saldo_pendiente <= 0){
                $doc->estado = 'pagada';
                $doc->saldo_pendiente = 0;
            }
            $doc->save();
            return response()->json(['data'=> new PagoResource($pago)],201);
        });
    }

    public function pagos($id)
    {
        $rows = CxcPago::where('cxc_id',$id)->orderBy('fecha_pago','desc')->get();
        return ['data'=> PagoResource::collection($rows)];
    }

    public function saldos(Request $request)
    {
        $query = CxcDocumento::query();
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->query('cliente_id'));
        }
        $total = (float) $query->sum('saldo_pendiente');
        $vencido = (float) (clone $query)->where('fecha_vencimiento', '<', now()->toDateString())->sum('saldo_pendiente');
        return [
            'cliente_id' => $request->query('cliente_id'),
            'saldo_total' => round($total, 2),
            'vencido' => round($vencido, 2),
            'no_vencido' => round($total - $vencido, 2),
        ];
    }

    public function anularPago($id)
    {
        $pago = CxcPago::find($id);
        if (!$pago) {
            return response()->json(['error' => 'NotFound', 'message' => 'Pago no encontrado'], 404);
        }
        return DB::transaction(function () use ($pago) {
            $doc = CxcDocumento::lockForUpdate()->find($pago->cxc_id);
            if ($doc) {
                $doc->saldo_pendiente = round($doc->saldo_pendiente + $pago->monto, 2);
                $doc->estado = 'pendiente';
                $doc->save();
            }
            $pago->delete();
            return ['message' => 'Pago anulado'];
        });
    }
}
