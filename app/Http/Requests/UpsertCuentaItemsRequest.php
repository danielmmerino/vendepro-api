<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertCuentaItemsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required','array','min:1'],
            'items.*.pedido_item_id' => ['required','uuid','exists:pedido_items,id'],
            'items.*.cantidad' => ['nullable','numeric','min:0.0001'],
            'items.*.monto' => ['nullable','numeric','min:0.01'],
            'items.*.notas' => ['nullable','string','max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            foreach ($this->input('items', []) as $idx => $item) {
                if (!isset($item['cantidad']) && !isset($item['monto'])) {
                    $v->errors()->add("items.$idx.cantidad", 'required_without:monto');
                }
            }
        });
    }
}
