<?php

namespace App\Services;

use App\Models\KdsSlaCategoria;
use App\Models\KdsSlaProducto;

class KdsSlaService
{
    public function get(): array
    {
        return [
            'por_categoria' => KdsSlaCategoria::all(['categoria_id','sla_seg']),
            'por_producto' => KdsSlaProducto::all(['producto_id','sla_seg'])
        ];
    }

    public function update(array $data): void
    {
        KdsSlaCategoria::truncate();
        KdsSlaProducto::truncate();
        foreach ($data['por_categoria'] ?? [] as $cat) {
            KdsSlaCategoria::create($cat);
        }
        foreach ($data['por_producto'] ?? [] as $prod) {
            KdsSlaProducto::create($prod);
        }
    }
}
