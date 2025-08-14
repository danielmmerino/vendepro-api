<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\NotaCredito */
class NotaCreditoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'factura_id' => $this->factura_id,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'motivo' => $this->motivo,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total' => $this->total,
            'estado' => $this->estado,
        ];
    }
}
