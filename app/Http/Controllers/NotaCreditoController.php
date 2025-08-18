<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotaCreditoRequest;
use App\Http\Resources\NotaCreditoResource;
use App\Models\NotaCredito;
use App\Models\CxcDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaCreditoController extends Controller
{
    public function index(Request $request)
    {
        $query = NotaCredito::query();
        if($request->filled('factura_id')) $query->where('factura_id',$request->query('factura_id'));
        if($request->filled('estado')) $query->where('estado',$request->query('estado'));
        if($request->filled('desde')) $query->whereDate('fecha','>=',$request->query('desde'));
        if($request->filled('hasta')) $query->whereDate('fecha','<=',$request->query('hasta'));
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $paginator = $query->orderBy('fecha','desc')->paginate($per,['*'],'page',$page);
        return [
            'data'=> NotaCreditoResource::collection($paginator->items()),
            'meta'=>[
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

    public function store(StoreNotaCreditoRequest $request)
    {
        $data = $request->validated();
        $nota = NotaCredito::create($data);
        return response()->json(['data'=> new NotaCreditoResource($nota)],201);
    }

    public function show($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=> new NotaCreditoResource($nota)];
    }


    public function update(StoreNotaCreditoRequest $request, $id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota || $nota->estado !== 'borrador'){
            return response()->json(['error'=>'Conflict','message'=>'Estado invalido'],409);
        }
        $nota->update($request->validated());
        return ['data'=> new NotaCreditoResource($nota)];
    }

    public function destroy($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota || $nota->estado !== 'borrador'){
            return response()->json(['error'=>'Conflict','message'=>'Estado invalido'],409);
        }
        $nota->delete();
        return response()->json([],204);
    }

    public function emitir($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        $nota->estado = 'emitida';
        $nota->save();
        return ['data'=>[
            'nota_credito_id'=>$nota->id,
            'numero'=>$nota->numero,
            'clave_acceso'=>'DEMO',
            'estado_sri'=>'AUTORIZADO',
            'mensajes'=>['AUTORIZADO'],
            'autorizado_at'=>now()->toIso8601String(),
        ]];
    }

    public function reintentarEnvio($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=>['status'=>'reintento']];
    }

    public function estadoSri($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=>['estado_sri'=>'AUTORIZADO']];
    }

    public function xml($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return response('<xml/>',200,['Content-Type'=>'application/xml']);
    }

    public function pdf($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return response('PDF',200,['Content-Type'=>'application/pdf']);
    }

    public function email(Request $request,$id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=>['enviado'=>true]];
    }

    public function reembolso(Request $request,$id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return response()->json(['data'=>['monto'=>$request->input('monto')]],201);
    }

    public function aplicar($id)
    {
        return DB::transaction(function() use ($id){
            $nota = NotaCredito::lockForUpdate()->find($id);
            if(!$nota){
                return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
            }
            if($nota->estado !== 'emitida'){
                return response()->json(['error'=>'Conflict','message'=>'Estado invalido'],409);
            }
            $doc = CxcDocumento::lockForUpdate()->where('factura_id',$nota->factura_id)->first();
            if(!$doc){
                return response()->json(['error'=>'NotFound','message'=>'CxC no encontrada'],404);
            }
            if($doc->saldo_pendiente <= 0){
                return response()->json(['error'=>'Unprocessable','message'=>'Sin saldo'],422);
            }
            $doc->saldo_pendiente = max(0, round($doc->saldo_pendiente - $nota->total,2));
            if($doc->saldo_pendiente <= 0){
                $doc->estado = 'pagada';
            }
            $doc->save();
            $nota->estado = 'aplicada';
            $nota->save();
            return ['data'=> new NotaCreditoResource($nota)];
        });
    }

    public function anular($id)
    {
        $nota = NotaCredito::find($id);
        if(!$nota){
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        if($nota->estado !== 'emitida'){
            return response()->json(['error'=>'Conflict','message'=>'Estado invalido'],409);
        }
        $nota->estado='anulada';
        $nota->save();
        return ['data'=> new NotaCreditoResource($nota)];
    }
}
