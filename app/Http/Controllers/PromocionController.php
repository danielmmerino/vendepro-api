<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Http\Requests\StorePromocionRequest;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promocion::query();
        if ($search = $request->query('search')) {
            $query->where('nombre', 'like', "%$search%");
        }
        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }
        if ($tipo = $request->query('tipo')) {
            $query->where('tipo', $tipo);
        }
        if ($canal = $request->query('canal')) {
            $query->where('canal', $canal);
        }
        $promos = $query->paginate($request->get('per_page', 15));
        return response()->json($promos);
    }

    public function store(StorePromocionRequest $request)
    {
        $promo = Promocion::create($request->validated());
        return response()->json(['data' => $promo], 201);
    }

    public function show($id)
    {
        $promo = Promocion::findOrFail($id);
        return response()->json(['data' => $promo]);
    }

    public function update(StorePromocionRequest $request, $id)
    {
        $promo = Promocion::findOrFail($id);
        $promo->update($request->validated());
        return response()->json(['data' => $promo]);
    }

    public function destroy($id)
    {
        $promo = Promocion::findOrFail($id);
        $promo->delete();
        return response()->json(null, 204);
    }

    public function activar($id)
    {
        $promo = Promocion::findOrFail($id);
        $promo->estado = 'activa';
        $promo->save();
        return response()->json(['data' => $promo]);
    }

    public function desactivar($id)
    {
        $promo = Promocion::findOrFail($id);
        $promo->estado = 'inactiva';
        $promo->save();
        return response()->json(['data' => $promo]);
    }

    public function duplicar($id)
    {
        $promo = Promocion::findOrFail($id);
        $copy = $promo->replicate();
        $copy->estado = 'inactiva';
        $copy->nombre = $promo->nombre . ' (copia)';
        $copy->save();
        return response()->json(['data' => $copy], 201);
    }
}
