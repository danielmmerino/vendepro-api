<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuentaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pedido_id' => $this->pedido_id,
            'nombre' => $this->nombre,
            'notas' => $this->notas,
            'subtotal' => (float) $this->subtotal,
            'descuento' => (float) $this->descuento,
            'impuesto' => (float) $this->impuesto,
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'items' => CuentaItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
