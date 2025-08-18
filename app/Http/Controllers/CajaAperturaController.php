<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Http\Requests\StoreCajaAperturaRequest;
use Illuminate\Http\Request;

class CajaAperturaController extends Controller
{
    public function store(StoreCajaAperturaRequest $request)
    {
        $data = $request->validated();

        $exists = CajaApertura::where('local_id', $data['local_id'])
            ->where('caja_id', $data['caja_id'])
            ->where('estado', 'abierta')
            ->first();
        if ($exists) {
            return response()->json([
                'error' => [
                    'code' => 'CONFLICT',
                    'message' => 'Ya existe apertura abierta'
                ]
            ], 409);
        }

        $apertura = CajaApertura::create($data);
        return response()->json(['data' => $apertura], 201);
    }

    public function index(Request $request)
    {
        $query = CajaApertura::query();
        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }
        if ($local = $request->query('local_id')) {
            $query->where('local_id', $local);
        }
        if ($usuario = $request->query('usuario_id')) {
            $query->where('usuario_id', $usuario);
        }
        if ($desde = $request->query('desde')) {
            $query->whereDate('abierto_at', '>=', $desde);
        }
        if ($hasta = $request->query('hasta')) {
            $query->whereDate('abierto_at', '<=', $hasta);
        }
        $per = min($request->query('per_page',20),100);
        $page = max($request->query('page',1),1);
        $aperturas = $query->paginate($per, ['*'], 'page', $page);
        return [
            'data' => $aperturas->items(),
            'meta' => [
                'page' => $aperturas->currentPage(),
                'per_page' => $aperturas->perPage(),
                'total' => $aperturas->total(),
            ]
        ];
    }

    public function show($id)
    {
        $apertura = CajaApertura::find($id);
        if (!$apertura) {
            return response()->json(['error' => ['code' => 'NOT_FOUND','message' => 'No encontrado']],404);
        }
        // Placeholder totals
        $apertura->totales_por_medio = [
            'efectivo' => 0,
            'tarjeta' => 0,
            'transferencia' => 0,
            'otros' => 0
        ];
        return ['data' => $apertura];
    }
}
