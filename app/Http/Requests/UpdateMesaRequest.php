<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('mesas', 'codigo')->ignore($id)],
            'nombre' => ['nullable', 'string', 'max:100'],
            'capacidad' => ['required', 'integer', 'min:1'],
            'ubicacion' => ['required', 'in:salon,terraza,barra,privado'],
            'estado' => ['required', 'in:activa,inactiva'],
            'notas' => ['nullable', 'string', 'max:255'],
        ];
    }
}
