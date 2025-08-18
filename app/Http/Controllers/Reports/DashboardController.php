<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportesService;

class DashboardController extends Controller
{
    public function __construct(protected ReportesService $service)
    {
    }

    public function resumen(Request $request)
    {
        $kpis = $this->service->calcularKpis(1250.40, 85.20, 139.82, 42.10, 97, 744.65);

        return response()->json([
            'data' => [
                'fecha' => $request->query('fecha', now()->toDateString()),
                'local_id' => $request->query('local_id'),
                'kpis' => $kpis,
                'comparacion' => [
                    'vs' => $request->query('comparar_con'),
                    'delta_porcentaje' => 0,
                ],
            ],
        ]);
    }
}
