<?php

namespace App\Http\Requests;

class SimularPromocionesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'local_id' => 'required|integer',
            'canal' => 'required|string',
            'cliente_id' => 'nullable|integer',
            'lineas' => 'required|array',
            'lineas.*.producto_id' => 'required|integer',
            'lineas.*.cantidad' => 'required|numeric',
            'lineas.*.precio_unitario' => 'required|numeric',
            'lineas.*.categoria_id' => 'nullable|integer',
            'cupones' => 'nullable|array',
        ];
    }
}
