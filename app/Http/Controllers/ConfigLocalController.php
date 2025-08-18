<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigLocalController extends Controller
{
    public function show($id)
    {
        $row = DB::selectOne("SELECT config FROM config_local WHERE local_id = :id", ['id' => $id]);
        $config = $row ? json_decode($row->config, true) : [];
        return ['data' => $config];
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'iva_incluido' => 'boolean',
            'propina_por_defecto' => 'numeric|between:0,100',
            'servicio_por_defecto' => 'numeric|between:0,100',
            'punto_emision' => 'nullable|string',
            'establecimiento' => 'nullable|string',
            'horarios' => 'array',
            'aforo' => 'integer',
            'kds_ruteo' => 'array',
            'impresoras' => 'array',
            'metodos_pago_habilitados' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $data = $validator->validated();
        $json = json_encode($data);
        $exists = DB::selectOne("SELECT local_id FROM config_local WHERE local_id = :id", ['id' => $id]);
        if ($exists) {
            DB::update(
                "UPDATE config_local SET config = :config, updated_by = :user, updated_at = CURRENT_TIMESTAMP WHERE local_id = :id",
                ['config' => $json, 'user' => $request->user()->id ?? null, 'id' => $id]
            );
        } else {
            DB::insert(
                "INSERT INTO config_local (local_id, config, updated_by, created_at, updated_at) VALUES (:id, :config, :user, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                ['id' => $id, 'config' => $json, 'user' => $request->user()->id ?? null]
            );
        }
        return ['data' => $data];
    }
}

