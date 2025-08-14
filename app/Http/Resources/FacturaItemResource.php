<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\FacturaItem */
class FacturaItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'concepto' => $this->concepto,
            'cantidad' => $this->cantidad,
            'precio_unit' => $this->precio_unit,
            'impuesto_porcentaje' => $this->impuesto_porcentaje,
            'total_linea' => $this->total_linea,
            'notas' => $this->notas,
        ];
    }
}
