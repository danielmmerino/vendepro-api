<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CostoController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'data' => [
                'metodo' => 'PROMEDIO_PONDERADO',
                'costo_promedio' => 0,
                'ultimo_costo' => 0,
                'fecha_ultimo_mov' => now()->toDateString(),
            ],
        ]);
    }

    public function recalcular(Request $request)
    {
        return response()->json([
            'data' => ['tracking_id' => 1],
        ], 202);
    }
}
