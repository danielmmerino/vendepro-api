<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBodegaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('bodegas', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'in:activa,inactiva'],
        ];
    }
}
