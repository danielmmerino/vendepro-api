<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoItemsRequest;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\KdsRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoItemController extends Controller
{
    public function __construct(private KdsRoutingService $routing) {}

    public function store(StorePedidoItemsRequest $request, Pedido $pedido): JsonResponse
    {
        $items = $this->routing->routeItems($request->validated()['items']);
        return response()->json(['data' => $items], 201);
    }

    public function update(Request $request, Pedido $pedido, PedidoItem $item): JsonResponse
    {
        return response()->json(['data' => $item]);
    }

    public function destroy(Pedido $pedido, PedidoItem $item): JsonResponse
    {
        return response()->json(['data' => true]);
    }

    public function addModifiers(Request $request, Pedido $pedido, PedidoItem $item): JsonResponse
    {
        return response()->json(['data' => $request->input('modificadores', [])]);
    }

    public function removeModifier(Pedido $pedido, PedidoItem $item, string $mod): JsonResponse
    {
        return response()->json(['data' => true]);
    }

    public function move(Request $request, Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => $request->input('items', [])]);
    }

    public function priority(Request $request, Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => $request->input('prioridad')]);
    }

    public function hold(Request $request, Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => 'hold']);
    }

    public function fire(Request $request, Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => 'fire']);
    }
}
