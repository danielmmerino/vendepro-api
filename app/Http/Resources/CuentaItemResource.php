<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuentaItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cuenta_id' => $this->cuenta_id,
            'pedido_item_id' => $this->pedido_item_id,
            'cantidad' => (float) $this->cantidad,
            'monto' => (float) $this->monto,
            'impuesto_monto' => (float) $this->impuesto_monto,
            'notas' => $this->notas,
            'created_at' => $this->created_at,
        ];
    }
}
