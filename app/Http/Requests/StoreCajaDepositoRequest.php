<?php

namespace App\Http\Requests;

class StoreCajaDepositoRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'apertura_id' => ['required','integer'],
            'monto' => ['required','numeric'],
            'banco' => ['required','string'],
            'referencia' => ['required','string'],
            'fecha_deposito' => ['required','date'],
        ];
    }
}
