<?php

namespace App\Http\Requests;

class ValidarCuponRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'codigo' => 'required|string',
            'cliente_id' => 'required|integer',
            'pedido_id' => 'required|integer',
        ];
    }
}
