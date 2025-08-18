<?php

namespace App\Services;

class ReportesService
{
    public function calcularKpis(
        float $ventasBrutas,
        float $descuentos,
        float $impuestos,
        float $propina,
        int $tickets,
        float $costo = 0
    ): array {
        $ventasNetas = $ventasBrutas - $descuentos;
        $ventasTotales = $ventasNetas + $impuestos + $propina;
        $ticketPromedio = $tickets > 0 ? round($ventasTotales / $tickets, 2) : 0;
        $margen = $ventasNetas - $costo;

        return [
            'ventas_brutas' => round($ventasBrutas, 2),
            'descuentos' => round($descuentos, 2),
            'ventas_netas' => round($ventasNetas, 2),
            'impuestos' => round($impuestos, 2),
            'propina' => round($propina, 2),
            'ventas_totales' => round($ventasTotales, 2),
            'tickets' => $tickets,
            'ticket_promedio' => $ticketPromedio,
            'margen' => round($margen, 2),
        ];
    }
}
