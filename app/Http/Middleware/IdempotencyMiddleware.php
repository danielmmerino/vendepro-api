<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');
        if (!$key) {
            return $next($request);
        }

        $hash = sha1($request->getContent());
        $entry = DB::table('idempotencies')->where('key', $key)->first();
        if ($entry) {
            abort(409, 'Duplicate request');
        }

        $response = $next($request);

        DB::table('idempotencies')->insert([
            'id' => (string)\Illuminate\Support\Str::uuid(),
            'key' => $key,
            'endpoint' => $request->path(),
            'body_hash' => $hash,
            'response_hash' => sha1($response->getContent()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $response;
    }
}
