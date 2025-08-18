<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnviarCocinaRequest;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;

class PedidoKdsController extends Controller
{
    public function send(EnviarCocinaRequest $request, Pedido $pedido): JsonResponse
    {
        return response()->json(['data' => ['comandas' => []]]);
    }
}
