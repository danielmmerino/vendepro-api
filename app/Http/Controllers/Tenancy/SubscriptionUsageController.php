<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionUsageController extends Controller
{
    public function show($id)
    {
        $rows = DB::table('subscription_usage')
            ->select('metric', 'valor')
            ->where('subscription_id', $id)
            ->where('fecha', DB::raw('CURRENT_DATE'))
            ->get();
        $data = [];
        foreach ($rows as $row) {
            $data[$row->metric] = $row->valor + 0;
        }
        return ['data' => $data];
    }

    public function consume(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'metric' => 'required',
            'incremento' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation', 'fields' => $validator->errors()->toArray()], 422);
        }
        $data = $validator->validated();
        $metric = $data['metric'];
        $inc = $data['incremento'];
        DB::statement('INSERT INTO subscription_usage (subscription_id, metric, valor, fecha, created_at, updated_at)
            VALUES (?, ?, ?, CURRENT_DATE, NOW(), NOW())
            ON DUPLICATE KEY UPDATE valor = valor + VALUES(valor), updated_at = NOW()', [$id, $metric, $inc]);
        $row = DB::table('subscription_usage')
            ->select('valor')
            ->where('subscription_id', $id)
            ->where('metric', $metric)
            ->where('fecha', DB::raw('CURRENT_DATE'))
            ->first();
        return ['data' => [$metric => $row->valor + 0]];
    }
}
