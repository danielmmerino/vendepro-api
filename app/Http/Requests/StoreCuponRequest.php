<?php

namespace App\Http\Requests;

class StoreCuponRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'promo_id' => 'required|integer',
            'codigo' => 'nullable|string',
            'vigencia_hasta' => 'required|date',
            'uso_max_por_cliente' => 'nullable|integer',
            'uso_max_global' => 'nullable|integer',
        ];
    }
}
