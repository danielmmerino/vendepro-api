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
            'local_id' => ['required', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'es_principal' => ['required', 'boolean'],
        ];
    }
}
