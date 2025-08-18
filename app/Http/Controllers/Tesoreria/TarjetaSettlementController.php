<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TarjetaSettlementController extends Controller
{
    public function store(Request $request)
    {
        return response()->json(['message' => 'Settlement importado'], 201);
    }
}
