<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KdsStreamController extends Controller
{
    public function stream(Request $request)
    {
        return response()->stream(function () {
            echo "event: ping\n";
            echo "data: {}\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    }
}
