<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransferenciaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => [],
            'meta' => ['page' => 1, 'per_page' => 20, 'total' => 0],
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'data' => ['transferencia_id' => 1, 'estado' => 'pendiente'],
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json([
            'data' => ['id' => $id, 'estado' => 'pendiente', 'lineas' => []],
        ]);
    }

    public function recibir(int $id, Request $request)
    {
        return response()->json([
            'data' => ['id' => $id, 'estado' => 'parcial'],
        ]);
    }

    public function cancelar(int $id)
    {
        return response()->json([
            'data' => ['id' => $id, 'estado' => 'cancelada'],
        ]);
    }
}
