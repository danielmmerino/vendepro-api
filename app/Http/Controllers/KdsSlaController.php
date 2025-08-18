<?php

namespace App\Http\Controllers;

use App\Services\KdsSlaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KdsSlaController extends Controller
{
    public function __construct(private KdsSlaService $service) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->service->get()]);
    }

    public function update(Request $request): JsonResponse
    {
        $this->service->update($request->all());
        return response()->json(['data' => $this->service->get()]);
    }
}
