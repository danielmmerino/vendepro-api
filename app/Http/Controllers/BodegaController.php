<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBodegaRequest;
use App\Http\Requests\UpdateBodegaRequest;
use App\Http\Resources\BodegaCollection;
use App\Http\Resources\BodegaResource;
use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BodegaController extends Controller
{
    public function index(Request $request)
    {
        $query = Bodega::query();

        if ($q = $request->query('q')) {
            $query->where(function ($q2) use ($q) {
                $q2->where('codigo', 'like', "%$q%")
                    ->orWhere('nombre', 'like', "%$q%");
            });
        }

        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }

        if ($sort = $request->query('sort')) {
            foreach (explode(',', $sort) as $part) {
                $direction = 'asc';
                $column = $part;
                if (str_starts_with($part, '-')) {
                    $direction = 'desc';
                    $column = substr($part, 1);
                }
                if (in_array($column, ['codigo', 'nombre'])) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        $perPage = min($request->query('per_page', 20), 100);
        $bodegas = $query->paginate($perPage);

        return new BodegaCollection($bodegas);
    }

    public function store(StoreBodegaRequest $request)
    {
        $bodega = Bodega::create($request->validated());
        return (new BodegaResource($bodega))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $bodega = Bodega::find($id);
        if (!$bodega) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return new BodegaResource($bodega);
    }

    public function update(UpdateBodegaRequest $request, $id)
    {
        $bodega = Bodega::find($id);
        if (!$bodega) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        $bodega->update($request->validated());
        return new BodegaResource($bodega);
    }

    public function destroy($id)
    {
        $bodega = Bodega::find($id);
        if (!$bodega) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $saldos = DB::table('inventario_saldos')
            ->where('bodega_id', $id)
            ->where('cantidad', '<>', 0)
            ->count();
        if ($saldos > 0) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'La bodega posee saldos',
            ], 409);
        }

        $bodega->delete();
        return response()->json(null, 204);
    }
}
