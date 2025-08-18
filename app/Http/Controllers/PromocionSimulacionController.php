<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimularPromocionesRequest;
use App\Http\Requests\AplicarPromocionesRequest;
use App\Services\PromocionService;

class PromocionSimulacionController extends Controller
{
    protected PromocionService $service;

    public function __construct(PromocionService $service)
    {
        $this->service = $service;
    }

    public function simular(SimularPromocionesRequest $request)
    {
        $result = $this->service->simular($request->validated());
        return response()->json(['data' => $result]);
    }

    public function aplicar(AplicarPromocionesRequest $request)
    {
        $result = $this->service->aplicar($request->validated());
        return response()->json(['data' => $result], 201);
    }
}
