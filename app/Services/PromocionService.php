<?php

namespace App\Services;

class PromocionService
{
    public function simular(array $payload): array
    {
        $total = 0;
        foreach ($payload['lineas'] ?? [] as $linea) {
            $total += ($linea['precio_unitario'] ?? 0) * ($linea['cantidad'] ?? 0);
        }
        return [
            'aplicadas' => [],
            'no_aplicadas' => [],
            'descuentos_por_linea' => [],
            'total_descuentos' => 0,
            'total_orden' => $total,
            'log' => [],
        ];
    }

    public function aplicar(array $payload): array
    {
        return ['mensaje' => 'aplicado'];
    }
}
