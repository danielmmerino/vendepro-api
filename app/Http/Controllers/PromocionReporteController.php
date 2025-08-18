<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromocionReporteController extends Controller
{
    public function efectividad(Request $request)
    {
        return response()->json(['data' => []]);
    }
}
