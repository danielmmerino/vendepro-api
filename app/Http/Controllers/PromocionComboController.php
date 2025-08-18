<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromocionComboRequest;
use App\Models\Promocion;
use App\Models\PromocionComboDetalle;

class PromocionComboController extends Controller
{
    public function store(PromocionComboRequest $request, $id)
    {
        $promo = Promocion::findOrFail($id);
        PromocionComboDetalle::where('promocion_id', $id)->delete();
        foreach ($request->validated()['items'] as $item) {
            PromocionComboDetalle::create([
                'promocion_id' => $id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
            ]);
        }
        $promo->recompensa = ['modo' => 'precio_fijo', 'valor' => $request->validated()['precio_combo']];
        $promo->save();
        return response()->json(['data' => $promo]);
    }
}
