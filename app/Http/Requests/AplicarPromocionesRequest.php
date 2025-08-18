<?php

namespace App\Http\Requests;

class AplicarPromocionesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'pedido_id' => 'required|integer',
            'simulacion_id' => 'nullable|string',
            'forzar_ids' => 'nullable|array',
        ];
    }
}
