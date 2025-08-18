<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConteoController extends Controller
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
            'data' => ['conteo_id' => 1, 'estado' => 'abierto'],
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json([
            'data' => ['id' => $id, 'estado' => 'abierto', 'diferencias' => []],
        ]);
    }

    public function capturas(int $id, Request $request)
    {
        return response()->json([
            'data' => ['capturas' => $request->input('capturas', [])],
        ], 201);
    }

    public function cerrar(int $id)
    {
        return response()->json([
            'data' => ['id' => $id, 'sobrantes' => 0, 'faltantes' => 0, 'total_lineas' => 0, 'valores_ajustados' => 0],
        ]);
    }
}
