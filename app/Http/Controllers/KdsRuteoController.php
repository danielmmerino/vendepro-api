<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\KdsRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KdsRuteoController extends Controller
{
    public function __construct(private KdsRoutingService $routing) {}

    public function test(Request $request): JsonResponse
    {
        $pedido = Pedido::find($request->query('pedido_id'));
        $items = $pedido?->items()->get()->toArray() ?? [];
        return response()->json(['data' => $this->routing->routeItems($items)]);
    }
}
