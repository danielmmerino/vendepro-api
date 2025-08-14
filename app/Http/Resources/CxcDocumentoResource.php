<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CxcDocumento */
class CxcDocumentoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'factura_id' => $this->factura_id,
            'cliente_id' => $this->cliente_id,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'total' => $this->total,
            'saldo_pendiente' => $this->saldo_pendiente,
            'estado' => $this->estado,
            'pagos' => PagoResource::collection($this->whenLoaded('pagos')),
        ];
    }
}
