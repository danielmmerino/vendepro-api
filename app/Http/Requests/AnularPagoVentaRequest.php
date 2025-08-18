<?php

namespace App\Http\Requests;

class AnularPagoVentaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'motivo' => ['required','string'],
        ];
    }
}
