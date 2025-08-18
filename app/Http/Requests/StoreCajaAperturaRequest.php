<?php

namespace App\Http\Requests;

class StoreCajaAperturaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'local_id' => ['required','integer'],
            'caja_id' => ['required','integer'],
            'usuario_id' => ['required','integer'],
            'saldo_inicial' => ['required','numeric'],
            'observacion' => ['nullable','string'],
        ];
    }
}
