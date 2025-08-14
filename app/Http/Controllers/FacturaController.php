<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use App\Http\Resources\FacturaResource;
use App\Models\FacturaVenta;
use App\Models\FacturaItem;
use App\Models\CxcDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = FacturaVenta::query();
        if ($request->filled('estado')) {
            $query->where('estado', $request->query('estado'));
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->query('cliente_id'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->query('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->query('hasta'));
        }
        if ($q = $request->query('q')) {
            $query->where(fn($s) => $s->where('numero','like',"%$q%")
                ->orWhere('notas','like',"%$q%"));
        }
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $paginator = $query->orderBy('fecha','desc')->orderBy('numero','desc')
            ->paginate($per,['*'],'page',$page);

        return [
            'data' => FacturaResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ],
        };
    }

    public function store(StoreFacturaRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {
            $factura = FacturaVenta::create($data);
            foreach ($data['items'] as $item) {
                $item['factura_id'] = $factura->id;
                FacturaItem::create($item);
            }
            $factura->load('items');
            return response()->json(['data' => new FacturaResource($factura)],201);
        });
    }

    public function show($id)
    {
        $factura = FacturaVenta::with('items')->find($id);
        if (!$factura) {
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        return ['data'=> new FacturaResource($factura)];
    }

    public function update(UpdateFacturaRequest $request, $id)
    {
        $factura = FacturaVenta::with('items')->find($id);
        if (!$factura) {
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        if ($factura->estado !== 'borrador') {
            return response()->json(['error'=>'Conflict','message'=>'Solo se puede editar en estado borrador'],409);
        }
        $data = $request->validated();
        return DB::transaction(function () use ($factura,$data) {
            $factura->update($data);
            $factura->items()->delete();
            foreach ($data['items'] as $item) {
                $item['factura_id'] = $factura->id;
                FacturaItem::create($item);
            }
            $factura->load('items');
            return ['data'=> new FacturaResource($factura)];
        });
    }

    public function destroy($id)
    {
        $factura = FacturaVenta::find($id);
        if (!$factura) {
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        if ($factura->estado !== 'borrador') {
            return response()->json(['error'=>'Unprocessable','message'=>'No se puede eliminar'],422);
        }
        $factura->delete();
        return response()->json(null,204);
    }

    public function aprobar($id)
    {
        return DB::transaction(function () use ($id) {
            $factura = FacturaVenta::lockForUpdate()->with('items')->find($id);
            if (!$factura) {
                return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
            }
            if ($factura->estado !== 'borrador') {
                return response()->json(['error'=>'Conflict','message'=>'Solo borrador'],409);
            }
            if ($factura->items()->count() === 0) {
                return response()->json(['error'=>'Unprocessable','message'=>'No tiene items'],422);
            }
            $factura->estado = 'aprobada';
            $factura->save();
            CxcDocumento::create([
                'factura_id'=>$factura->id,
                'cliente_id'=>$factura->cliente_id,
                'fecha_emision'=>$factura->fecha,
                'fecha_vencimiento'=>$factura->fecha,
                'total'=>$factura->total,
                'saldo_pendiente'=>$factura->total,
            ]);
            $factura->load('items');
            return ['data'=> new FacturaResource($factura)];
        });
    }

    public function anular($id)
    {
        $factura = FacturaVenta::find($id);
        if (!$factura) {
            return response()->json(['error'=>'NotFound','message'=>'Recurso no encontrado'],404);
        }
        if ($factura->estado !== 'borrador') {
            return response()->json(['error'=>'Conflict','message'=>'Solo borrador'],409);
        }
        $factura->estado = 'anulada';
        $factura->save();
        return ['data'=> new FacturaResource($factura)];
    }
}
