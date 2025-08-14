<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CxcPago */
class PagoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'fecha_pago' => $this->fecha_pago,
            'monto' => $this->monto,
            'forma_pago' => $this->forma_pago,
            'referencia' => $this->referencia,
        ];
    }
}
