<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotaCreditoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'factura_id' => ['required','uuid'],
            'numero' => ['required','string','max:40','unique:cxc_notas_credito,numero'],
            'fecha' => ['required','date'],
            'motivo' => ['required','string','max:160'],
            'subtotal' => ['required','numeric'],
            'impuesto' => ['required','numeric'],
            'total' => ['required','numeric','gt:0'],
        ];
    }
}
