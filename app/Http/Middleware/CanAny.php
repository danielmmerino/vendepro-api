<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanAny
{
    public function handle(Request $request, Closure $next, ...$abilities)
    {
        $user = $request->user();
        foreach ($abilities as $ability) {
            if ($user && $user->can($ability)) {
                return $next($request);
            }
        }
        abort(403);
    }
}
