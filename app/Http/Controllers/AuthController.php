<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function __construct(protected JwtService $jwt)
    {
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user->load('local');
            if ($user->local && $user->local->subscription_expires_at && $user->local->subscription_expires_at->isPast()) {
                return response()->json(['message' => 'Subscription inactive'], 403);
            }

            $token = $this->jwt->generate($user);

            return [
                'token' => $token,
                'local_id' => $user->local_id,
            ];
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->increment('token_version');

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function refresh(Request $request)
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $this->jwt->decode(substr($auth, 7));
        if (!$payload) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($payload['sub'] ?? null);
        if (!$user || $user->token_version !== ($payload['token_version'] ?? null)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $this->jwt->refresh($payload);

        return ['token' => $token];
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->password_hash = $data['password'];
        $user->save();
        $user->increment('token_version');

        return response()->json(['message' => 'Password updated']);
    }

    public function forceInvalidate(Request $request)
    {
        $user = $request->user();
        $user->increment('token_version');

        return response()->json(['message' => 'Sessions revoked']);
    }
}
