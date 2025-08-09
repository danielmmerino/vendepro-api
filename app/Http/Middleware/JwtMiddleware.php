<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function __construct(protected JwtService $jwt)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($auth, 7);
        $payload = $this->jwt->decode($token);
        if (!$payload) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($payload['sub'] ?? null);
        if (!$user || $user->token_version !== ($payload['token_version'] ?? null)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->load('local');
        if ($user->local && $user->local->subscription_expires_at && $user->local->subscription_expires_at->isPast()) {
            return response()->json(['message' => 'Subscription inactive'], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
