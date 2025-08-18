<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ContextController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $local = DB::selectOne('SELECT l.id, l.nombre, e.id as empresa_id, e.nombre_comercial FROM locals l JOIN empresas e ON e.id = l.empresa_id WHERE l.id = ?', [$user->local_id]);
        if (!$local) {
            return response()->json(['error' => 'NotFound', 'message' => 'Recurso no encontrado'], 404);
        }
        return ['data' => [
            'empresa_id' => $local->empresa_id,
            'empresa_nombre' => $local->nombre_comercial,
            'suscripcion_estado' => 'active',
            'plan' => 'starter',
            'locales' => [
                ['id' => $local->id, 'nombre' => $local->nombre],
            ],
        ]];
    }
}
