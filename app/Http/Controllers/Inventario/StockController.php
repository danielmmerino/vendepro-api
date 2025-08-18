<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => [],
            'meta' => ['page' => 1, 'per_page' => 20, 'total' => 0],
        ]);
    }

    public function kardex(Request $request)
    {
        return response()->json([
            'data' => [],
            'meta' => ['page' => 1, 'per_page' => 20, 'total' => 0],
        ]);
    }
}
