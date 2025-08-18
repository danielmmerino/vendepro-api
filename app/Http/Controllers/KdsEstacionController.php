<?php

namespace App\Http\Controllers;

use App\Models\KdsEstacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KdsEstacionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => KdsEstacion::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $est = KdsEstacion::create($request->all());
        return response()->json(['data' => $est], 201);
    }

    public function show(KdsEstacion $kdsEstacion): JsonResponse
    {
        return response()->json(['data' => $kdsEstacion]);
    }

    public function update(Request $request, KdsEstacion $kdsEstacion): JsonResponse
    {
        $kdsEstacion->update($request->all());
        return response()->json(['data' => $kdsEstacion]);
    }

    public function destroy(KdsEstacion $kdsEstacion): JsonResponse
    {
        $kdsEstacion->delete();
        return response()->json(['data' => true]);
    }
}
