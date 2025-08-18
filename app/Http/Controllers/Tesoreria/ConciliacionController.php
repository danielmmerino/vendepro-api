<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConciliacionController extends Controller
{
    public function importEstado(Request $request)
    {
        return response()->json(['message' => 'Estado importado'], 201);
    }

    public function match($id, Request $request)
    {
        return ['message' => 'Match confirmado', 'conciliacion_id' => $id];
    }

    public function cerrar($id)
    {
        return ['message' => 'Conciliacion cerrada', 'conciliacion_id' => $id];
    }
}
