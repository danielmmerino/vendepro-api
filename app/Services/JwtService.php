<?php

namespace App\Services;

use App\Models\User;

class JwtService
{
    protected string $secret;
    protected int $ttl;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'secret');
        $this->ttl = 60 * 60 * 24; // 1 day
    }

    public function generate(User $user): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'sub' => $user->id,
            'token_version' => $user->token_version,
            'exp' => time() + $this->ttl,
        ];

        return $this->encode($header, $payload);
    }

    public function refresh(array $payload): string
    {
        $payload['exp'] = time() + $this->ttl;
        return $this->encode(['alg' => 'HS256', 'typ' => 'JWT'], $payload);
    }

    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header64, $payload64, $signature] = $parts;
        $validSignature = $this->sign("$header64.$payload64");
        if (!hash_equals($validSignature, $signature)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payload64), true);
        if (!$payload || ($payload['exp'] ?? 0) < time()) {
            return null;
        }

        return $payload;
    }

    protected function encode(array $header, array $payload): string
    {
        $header64 = $this->base64UrlEncode(json_encode($header));
        $payload64 = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->sign("$header64.$payload64");
        return "$header64.$payload64.$signature";
    }

    protected function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->secret, true));
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
