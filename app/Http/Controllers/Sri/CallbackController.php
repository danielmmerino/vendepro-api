<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function receive(Request $request)
    {
        return ['data'=>['ok'=>true]];
    }
}
