<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;
use App\Models\Sri\Establecimiento;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    public function index()
    {
        return ['data'=>Establecimiento::all()];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'emisor_id'=>'required|string',
            'codigo'=>'required|string',
            'direccion'=>'required|string',
            'nombre'=>'nullable|string'
        ]);
        $est = Establecimiento::create($data);
        return response()->json(['data'=>$est],201);
    }

    public function show($id)
    {
        $est = Establecimiento::find($id);
        if (!$est) return response()->json(['error'=>['message'=>'Not found']],404);
        return ['data'=>$est];
    }

    public function update(Request $request, $id)
    {
        $est = Establecimiento::find($id);
        if (!$est) return response()->json(['error'=>['message'=>'Not found']],404);
        $data = $request->validate([
            'direccion'=>'required|string',
            'nombre'=>'nullable|string'
        ]);
        $est->update($data);
        return ['data'=>$est];
    }

    public function destroy($id)
    {
        $est = Establecimiento::find($id);
        if (!$est) return response()->json(['error'=>['message'=>'Not found']],404);
        $est->delete();
        return response()->json(null,204);
    }
}
