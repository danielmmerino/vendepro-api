<?php

namespace App\Services;

use App\Models\KdsEstacion;

class KdsRoutingService
{
    public function routeItems(array $items): array
    {
        // Placeholder routing logic
        return array_map(function ($item) {
            $item['estacion_id'] = $item['estacion_id'] ?? null;
            return $item;
        }, $items);
    }
}
