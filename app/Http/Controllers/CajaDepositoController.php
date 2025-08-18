<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\CajaApertura;
use App\Http\Requests\StoreCajaDepositoRequest;

class CajaDepositoController extends Controller
{
    public function store(StoreCajaDepositoRequest $request)
    {
        $data = $request->validated();
        $apertura = CajaApertura::find($data['apertura_id']);
        if (!$apertura || $apertura->estado !== 'abierta') {
            return response()->json([
                'error' => ['code' => 'CONFLICT','message' => 'Apertura no disponible']
            ],409);
        }
        $data['tipo'] = 'deposito';
        $mov = CajaMovimiento::create($data);
        return response()->json(['data' => $mov],201);
    }
}
