<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * @OA\Get(path="/v1/pedidos", summary="Listar pedidos", @OA\Response(response=200, description="OK"))
     */
    public function index(Request $request): JsonResponse
    {
        $pedidos = Pedido::query()->paginate();
        return response()->json([
            'data' => PedidoResource::collection($pedidos),
            'meta' => ['current_page' => $pedidos->currentPage(), 'total' => $pedidos->total()],
            'links' => ['self' => $request->fullUrl()],
        ]);
    }

    /**
     * @OA\Post(
     *   path="/v1/pedidos",
     *   summary="Crear pedido",
     *   @OA\RequestBody(required=true, @OA\JsonContent(required={"origen"},
     *       @OA\Property(property="origen", type="string", example="salon")
     *   )),
     *   @OA\Response(response=201, description="Pedido creado")
     * )
     */
    public function store(StorePedidoRequest $request): JsonResponse
    {
        $pedido = DB::transaction(fn() => Pedido::create($request->validated()));
        return response()->json(['data' => new PedidoResource($pedido)], 201);
    }

    /**
     * @OA\Get(path="/v1/pedidos/{id}", summary="Mostrar pedido", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function show(Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => new PedidoResource($pedido)]);
    }

    /**
     * @OA\Put(path="/v1/pedidos/{id}", summary="Actualizar pedido", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function update(UpdatePedidoRequest $request, Pedido $pedido): JsonResponse
    {
        $pedido->update($request->validated());
        return response()->json(['data' => new PedidoResource($pedido)]);
    }

    /**
     * @OA\Delete(path="/v1/pedidos/{id}", summary="Eliminar pedido", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function destroy(Pedido $pedido): JsonResponse
    {
        $pedido->delete();
        return response()->json(['data' => true]);
    }
}
