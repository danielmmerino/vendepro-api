<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\FacturaVenta */
class FacturaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'cliente_id' => $this->cliente_id,
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'impuesto' => $this->impuesto,
            'total' => $this->total,
            'estado' => $this->estado,
            'items' => FacturaItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
