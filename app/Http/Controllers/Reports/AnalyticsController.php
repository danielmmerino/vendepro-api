<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportesService;

class AnalyticsController extends Controller
{
    public function __construct(protected ReportesService $service)
    {
    }

    public function kpis(Request $request)
    {
        $data = [
            [
                'fecha' => $request->query('desde'),
                'ventas_totales' => 100,
            ],
            [
                'fecha' => $request->query('hasta'),
                'ventas_totales' => 200,
            ],
        ];

        return response()->json(['data' => $data]);
    }

    public function query(Request $request)
    {
        return response()->json(['data' => []]);
    }
}
