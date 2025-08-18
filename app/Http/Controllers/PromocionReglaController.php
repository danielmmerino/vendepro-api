<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromocionReglaRequest;
use App\Models\Promocion;

class PromocionReglaController extends Controller
{
    public function store(PromocionReglaRequest $request, $id)
    {
        $promo = Promocion::findOrFail($id);
        $data = $request->validated();
        $promo->condiciones = $data['condiciones'];
        $promo->recompensa = $data['recompensa'];
        $promo->save();
        return response()->json(['data' => $promo]);
    }
}
