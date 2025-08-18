<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Http\Requests\StoreCuponRequest;
use App\Http\Requests\GenerarCuponesRequest;
use App\Http\Requests\ValidarCuponRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CuponController extends Controller
{
    public function index(Request $request)
    {
        $query = Cupon::query();
        if ($promo = $request->query('promo_id')) {
            $query->where('promo_id', $promo);
        }
        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }
        if ($search = $request->query('search')) {
            $query->where('codigo', 'like', "%$search%");
        }
        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(StoreCuponRequest $request)
    {
        $data = $request->validated();
        if (empty($data['codigo'])) {
            $data['codigo'] = Str::upper(Str::random(8));
        }
        $cupon = Cupon::create($data);
        return response()->json(['data' => $cupon], 201);
    }

    public function generarMasivo(GenerarCuponesRequest $request)
    {
        $data = $request->validated();
        $cupones = [];
        for ($i = 0; $i < $data['cantidad']; $i++) {
            $code = $data['prefijo'] . Str::upper(Str::random($data['longitud']));
            $cupones[] = Cupon::create([
                'promo_id' => $data['promo_id'],
                'codigo' => $code,
                'vigencia_hasta' => now()->addMonth(),
            ]);
        }
        return response()->json(['data' => $cupones], 201);
    }

    public function validar(ValidarCuponRequest $request)
    {
        $cupon = Cupon::where('codigo', $request->validated()['codigo'])->first();
        if (!$cupon || $cupon->estado !== 'activo') {
            return response()->json(['valido' => false, 'motivo' => 'invalido']);
        }
        return response()->json(['valido' => true, 'promo_id' => $cupon->promo_id, 'motivo' => null]);
    }

    public function anular($id)
    {
        $cupon = Cupon::findOrFail($id);
        if ($cupon->estado !== 'usado') {
            $cupon->estado = 'anulado';
            $cupon->save();
        }
        return response()->json(['data' => $cupon]);
    }
}
