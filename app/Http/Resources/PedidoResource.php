<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'mesa_id' => $this->mesa_id,
            'cliente_nombre' => $this->cliente_nombre,
            'estado' => $this->estado,
            'origen' => $this->origen,
            'notas' => $this->notas,
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'impuesto' => $this->impuesto,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
