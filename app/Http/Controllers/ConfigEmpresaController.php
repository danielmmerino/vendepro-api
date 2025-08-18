<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigEmpresaController extends Controller
{
    public function show()
    {
        $row = DB::selectOne("SELECT config FROM config_empresa WHERE id = 1");
        $config = $row ? json_decode($row->config, true) : [];
        return ['data' => $config];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_comercial' => 'nullable|string',
            'moneda' => 'nullable|string',
            'iva_incluido' => 'boolean',
            'redondeo_regla' => 'nullable|string',
            'propina_por_defecto' => 'numeric|between:0,100',
            'servicio_por_defecto' => 'numeric|between:0,100',
            'politica_stock_negativo' => 'boolean',
            'metodo_valuacion' => 'nullable|string',
            'exige_lote_caducidad' => 'boolean',
            'politica_seguridad' => 'array',
            'branding' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $json = json_encode($data);
        $exists = DB::selectOne("SELECT id FROM config_empresa WHERE id = 1");
        if ($exists) {
            DB::update(
                "UPDATE config_empresa SET config = :config, updated_by = :user, updated_at = CURRENT_TIMESTAMP WHERE id = 1",
                ['config' => $json, 'user' => $request->user()->id ?? null]
            );
        } else {
            DB::insert(
                "INSERT INTO config_empresa (id, config, updated_by, created_at, updated_at) VALUES (1, :config, :user, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                ['config' => $json, 'user' => $request->user()->id ?? null]
            );
        }
        return ['data' => $data];
    }
}

