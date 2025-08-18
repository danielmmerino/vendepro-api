<?php

namespace App\Http\Requests;

class GenerarCuponesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'promo_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
            'prefijo' => 'required|string',
            'longitud' => 'required|integer|min:1',
        ];
    }
}
