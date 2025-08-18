<?php

namespace App\Http\Requests;

class PromocionComboRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer',
            'items.*.cantidad' => 'required|numeric',
            'precio_combo' => 'required|numeric',
        ];
    }
}
