<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CuentaBancariaController extends Controller
{
    public function index()
    {
        return ['data' => []];
    }

    public function store(Request $request)
    {
        return response()->json(['data' => ['id' => 1]], 201);
    }
}
