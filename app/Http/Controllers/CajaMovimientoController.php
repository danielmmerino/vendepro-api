<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\CajaApertura;
use App\Http\Requests\StoreCajaMovimientoRequest;
use Illuminate\Http\Request;

class CajaMovimientoController extends Controller
{
    public function store(StoreCajaMovimientoRequest $request)
    {
        $data = $request->validated();
        $apertura = CajaApertura::find($data['apertura_id']);
        if (!$apertura || $apertura->estado !== 'abierta') {
            return response()->json([
                'error' => ['code' => 'CONFLICT','message' => 'Apertura no disponible']
            ],409);
        }
        $mov = CajaMovimiento::create($data);
        return response()->json(['data' => $mov],201);
    }

    public function index(Request $request)
    {
        $query = CajaMovimiento::query();
        if ($apertura = $request->query('apertura_id')) {
            $query->where('apertura_id', $apertura);
        }
        if ($tipo = $request->query('tipo')) {
            $query->where('tipo', $tipo);
        }
        if ($desde = $request->query('desde')) {
            $query->whereDate('created_at','>=',$desde);
        }
        if ($hasta = $request->query('hasta')) {
            $query->whereDate('created_at','<=',$hasta);
        }
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $movs = $query->paginate($per,['*'],'page',$page);
        return [
            'data' => $movs->items(),
            'meta' => [
                'page' => $movs->currentPage(),
                'per_page' => $movs->perPage(),
                'total' => $movs->total(),
            ]
        ];
    }
}
