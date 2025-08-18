<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use Illuminate\Http\Request;

class CajaEstadoController extends Controller
{
    public function index(Request $request)
    {
        $local = $request->query('local_id');
        $caja = $request->query('caja_id');
        $apertura = CajaApertura::where('local_id',$local)
            ->where('caja_id',$caja)
            ->where('estado','abierta')
            ->first();
        if ($apertura) {
            return [
                'abierta' => true,
                'apertura_id' => $apertura->id,
                'usuario_id' => $apertura->usuario_id,
                'saldo_efectivo' => $apertura->saldo_inicial,
            ];
        }
        return ['abierta' => false];
    }
}
