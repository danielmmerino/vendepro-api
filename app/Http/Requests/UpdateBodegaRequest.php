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
            'local_id' => ['required', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'es_principal' => ['required', 'boolean'],
        ];
    }
}
