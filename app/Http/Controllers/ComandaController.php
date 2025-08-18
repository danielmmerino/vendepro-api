<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComandaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function show(Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => $comanda]);
    }

    public function start(Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => 'start']);
    }

    public function ready(Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => 'ready']);
    }

    public function bump(Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => 'bump']);
    }

    public function recall(Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => 'recall']);
    }

    public function reassign(Request $request, Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => $request->input('estacion_id')]);
    }

    public function note(Request $request, Comanda $comanda): JsonResponse
    {
        return response()->json(['data' => $request->input('nota')]);
    }
}
