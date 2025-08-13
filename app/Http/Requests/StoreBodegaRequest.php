<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBodegaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', 'unique:bodegas,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'in:activa,inactiva'],
        ];
    }
}
