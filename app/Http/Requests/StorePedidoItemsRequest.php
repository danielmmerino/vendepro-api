<?php

namespace App\Http\Requests;

class StorePedidoItemsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required','array','min:1'],
            'items.*.producto_id' => ['required','uuid'],
            'items.*.descripcion' => ['required','string'],
            'items.*.cantidad' => ['required','numeric'],
            'items.*.precio_unitario' => ['required','numeric'],
            'items.*.curso' => ['nullable','integer'],
            'items.*.estacion_id' => ['nullable','uuid'],
            'items.*.modificadores' => ['nullable','array'],
        ];
    }
}
