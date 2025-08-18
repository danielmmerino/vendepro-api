<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function ventasDia(Request $request)
    {
        $data = [
            ['hora' => '08', 'tickets' => 5, 'ventas_totales' => 123.40],
        ];
        return response()->json(['data' => $data]);
    }

    public function ventas(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function topProductos(Request $request)
    {
        return response()->json([
            'data' => [],
            'meta' => ['top' => (int)$request->query('top', 10)],
        ]);
    }

    public function categorias(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function inventarioBajo(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function rotacion(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function caja(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function metodosPago(Request $request)
    {
        return response()->json(['data' => []]);
    }

    public function cxc(Request $request)
    {
        return response()->json(['data' => [
            'saldo_total' => 0,
            'vencido' => 0,
            'aging' => [],
        ]]);
    }

    public function cxp(Request $request)
    {
        return response()->json(['data' => [
            'saldo_total' => 0,
            'vencido' => 0,
            'aging' => [],
        ]]);
    }

    public function kdsTiempos(Request $request)
    {
        return response()->json(['data' => [
            'prep_promedio_seg' => 0,
            'por_categoria' => [],
            'p95_seg' => 0,
        ]]);
    }

    public function export(Request $request)
    {
        $jobId = 'EXP-' . now()->format('Ymd-His');
        return response()->json(['data' => ['job_id' => $jobId]], 202);
    }

    public function exportStatus(string $jobId)
    {
        return response()->json(['data' => ['estado' => 'procesando']]);
    }
}
