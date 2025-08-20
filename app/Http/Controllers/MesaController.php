<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Http\Resources\MesaCollection;
use App\Http\Resources\MesaResource;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MesaController extends Controller
{
    public function index(Request $request)
    {
        if (!$local = $request->query('local_id')) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['local_id' => ['Requerido']],
            ], 422);
        }

        $query = Mesa::query()->where('local_id', $local);

        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }

        if ($ubicacion = $request->query('ubicacion')) {
            $query->where('ubicacion', $ubicacion);
        }

        if ($capMin = $request->query('capacidad_min')) {
            $query->where('capacidad', '>=', (int) $capMin);
        }

        if ($capMax = $request->query('capacidad_max')) {
            $query->where('capacidad', '<=', (int) $capMax);
        }

        if ($q = $request->query('q')) {
            $query->where(function ($q2) use ($q) {
                $q2->where('codigo', 'like', "%$q%")
                    ->orWhere('nombre', 'like', "%$q%");
            });
        }

        if ($sort = $request->query('sort')) {
            foreach (explode(',', $sort) as $part) {
                $direction = 'asc';
                $column = $part;
                if (str_starts_with($part, '-')) {
                    $direction = 'desc';
                    $column = substr($part, 1);
                }
                if (in_array($column, ['codigo', 'nombre', 'capacidad', 'estado', 'ubicacion'])) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        $perPage = min($request->query('per_page', 20), 100);
        $mesas = $query->paginate($perPage);

        return new MesaCollection($mesas);
    }

    public function store(StoreMesaRequest $request)
    {
        $mesa = Mesa::create($request->validated());
        return (new MesaResource($mesa))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $mesa = Mesa::find($id);
        if (!$mesa) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        return new MesaResource($mesa);
    }

    public function update(UpdateMesaRequest $request, $id)
    {
        $mesa = Mesa::find($id);
        if (!$mesa) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }
        $mesa->update($request->validated());
        return new MesaResource($mesa);
    }

    public function destroy($id)
    {
        $mesa = Mesa::find($id);
        if (!$mesa) {
            return response()->json([
                'error' => 'NotFound',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        $futuras = DB::table('reservas')
            ->where('mesa_id', $id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('inicio', '>=', now())
            ->count();

        if ($futuras > 0) {
            return response()->json([
                'error' => 'Unprocessable',
                'message' => 'La mesa tiene reservas futuras',
            ], 422);
        }

        $mesa->delete();
        return response()->json(null, 204);
    }
}
