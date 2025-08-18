<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MermaController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'data' => ['id' => 1],
        ], 201);
    }
}
