<?php

namespace App\Http\Requests;

class StoreCajaCierreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'apertura_id' => ['required','integer'],
            'conteo_efectivo' => ['nullable','array'],
            'conteo_efectivo.*' => ['integer'],
            'efectivo_total_contado' => ['required','numeric'],
            'observacion' => ['nullable','string'],
        ];
    }
}
