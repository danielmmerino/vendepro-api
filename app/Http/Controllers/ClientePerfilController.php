<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ClientePerfilController extends Controller
{
    public function show(int $id)
    {
        $row = DB::selectOne(
            "SELECT total_compras, tickets, ticket_promedio, ultima_compra, categoria_top_id, producto_top_id, rfm_json FROM clientes_kpis WHERE cliente_id = :id",
            ['id' => $id]
        );
        if (!$row) {
            return response()->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => 'Recurso no encontrado'
                ]
            ], 404);
        }

        $kpis = [
            'total_compras' => (float) $row->total_compras,
            'tickets' => (int) $row->tickets,
            'ticket_promedio' => (float) $row->ticket_promedio,
            'ultima_compra' => $row->ultima_compra,
            'categoria_top' => $row->categoria_top_id ? ['id' => (int)$row->categoria_top_id] : null,
            'producto_top' => $row->producto_top_id ? ['id' => (int)$row->producto_top_id] : null,
        ];

        return [
            'data' => [
                'kpis' => $kpis,
                'rfm' => $row->rfm_json ? json_decode($row->rfm_json, true) : null,
            ],
        ];
    }
}
