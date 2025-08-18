<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $activos = $request->query('activos');
        $query = Plan::query();
        if (!is_null($activos)) {
            $query->where('activo', filter_var($activos, FILTER_VALIDATE_BOOLEAN));
        }
        return ['data' => $query->get()];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|unique:plans,codigo',
            'nombre' => 'required',
            'precio_mensual' => 'numeric',
            'precio_anual' => 'numeric',
            'trial_dias' => 'integer',
            'limites' => 'array',
            'features' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation', 'fields' => $validator->errors()->toArray()], 422);
        }
        $plan = Plan::create($validator->validated());
        return response()->json(['data' => $plan], 201);
    }

    public function show($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        return ['data' => $plan];
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required',
            'precio_mensual' => 'sometimes|numeric',
            'precio_anual' => 'sometimes|numeric',
            'trial_dias' => 'sometimes|integer',
            'limites' => 'sometimes|array',
            'features' => 'sometimes|array',
            'activo' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation', 'fields' => $validator->errors()->toArray()], 422);
        }
        $plan->update($validator->validated());
        return ['data' => $plan];
    }

    public function destroy($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        $plan->delete();
        return response()->json([], 204);
    }

    public function getFeatures($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        return ['data' => [
            'features' => $plan->features ?? [],
            'limites' => $plan->limites ?? [],
        ]];
    }

    public function updateFeatures(Request $request, $id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        $validator = Validator::make($request->all(), [
            'features' => 'required|array',
            'limites' => 'nullable|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation', 'fields' => $validator->errors()->toArray()], 422);
        }
        $plan->update($validator->validated());
        return ['data' => [
            'features' => $plan->features,
            'limites' => $plan->limites,
        ]];
    }
}
