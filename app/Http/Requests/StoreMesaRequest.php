<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', 'unique:mesas,codigo'],
            'nombre' => ['nullable', 'string', 'max:100'],
            'capacidad' => ['required', 'integer', 'min:1'],
            'ubicacion' => ['required', 'in:salon,terraza,barra,privado'],
            'estado' => ['required', 'in:activa,inactiva'],
            'notas' => ['nullable', 'string', 'max:255'],
        ];
    }
}
