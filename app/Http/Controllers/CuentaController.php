<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCuentaRequest;
use App\Http\Requests\UpdateCuentaRequest;
use App\Http\Resources\CuentaResource;
use App\Models\Cuenta;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentaController extends Controller
{
    /**
     * @OA\Get(path="/v1/cuentas", summary="Listar cuentas", @OA\Response(response=200, description="OK"))
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cuenta::query();
        if ($pedidoId = $request->query('pedido_id')) {
            $query->where('pedido_id', $pedidoId);
        }
        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }
        if ($q = $request->query('q')) {
            $query->where(function ($qr) use ($q) {
                $qr->where('nombre', 'like', "%{$q}%")
                   ->orWhere('notas', 'like', "%{$q}%");
            });
        }
        $cuentas = $query->paginate();
        return response()->json([
            'data' => CuentaResource::collection($cuentas),
            'meta' => ['current_page' => $cuentas->currentPage(), 'total' => $cuentas->total()],
            'links' => ['self' => $request->fullUrl()],
        ]);
    }

    /**
     * @OA\Post(
     *   path="/v1/cuentas",
     *   summary="Crear cuenta",
     *   @OA\RequestBody(required=true, @OA\JsonContent(required={"pedido_id","nombre"},
     *       @OA\Property(property="pedido_id", type="string", format="uuid"),
     *       @OA\Property(property="nombre", type="string", example="Cuenta 1"),
     *       @OA\Property(property="notas", type="string", example="nota")
     *   )),
     *   @OA\Response(response=201, description="Cuenta creada")
     * )
     */
    public function store(StoreCuentaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $pedido = Pedido::find($data['pedido_id']);
        if (!$pedido || $pedido->estado === 'anulado') {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['pedido_id' => ['invalid']],
            ], 422);
        }
        $cuenta = DB::transaction(fn () => Cuenta::create($data));
        return response()->json(['data' => new CuentaResource($cuenta)], 201);
    }

    /**
     * @OA\Get(path="/v1/cuentas/{id}", summary="Mostrar cuenta", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function show(Cuenta $cuenta): JsonResponse
    {
        return response()->json(['data' => new CuentaResource($cuenta->load('items'))]);
    }

    /**
     * @OA\Put(path="/v1/cuentas/{id}", summary="Actualizar cuenta", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function update(UpdateCuentaRequest $request, Cuenta $cuenta): JsonResponse
    {
        if ($cuenta->estado !== 'abierta' && !$request->filled('estado')) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Cuenta no editable',
            ], 409);
        }
        $data = $request->validated();
        return DB::transaction(function () use ($cuenta, $data) {
            if (($data['estado'] ?? null) === 'cerrada' && $cuenta->items()->count() === 0) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['estado' => ['sin_items']],
                ], 422);
            }
            $cuenta->update($data);
            return response()->json(['data' => new CuentaResource($cuenta)]);
        });
    }

    /**
     * @OA\Delete(path="/v1/cuentas/{id}", summary="Eliminar cuenta", @OA\Parameter(name="id", in="path", required=true, schema=@OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
     */
    public function destroy(Cuenta $cuenta): JsonResponse
    {
        if ($cuenta->estado === 'cerrada') {
            return response()->json([
                'error' => 'Validation',
                'message' => 'No se puede eliminar una cuenta cerrada',
            ], 422);
        }
        return DB::transaction(function () use ($cuenta) {
            $cuenta->estado = 'anulada';
            $cuenta->save();
            $cuenta->items()->delete();
            $cuenta->delete();
            return response()->json(['data' => true]);
        });
    }
}
