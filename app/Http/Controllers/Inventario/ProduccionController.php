<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'data' => ['id' => 1],
        ], 201);
    }
}
