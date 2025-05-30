<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class JWTService
{
    private $key;
    private $algorithm;

    public function __construct()
    {
        $this->key = env('JWT_SECRET', 'your-secret-key-change-this-in-production');
        $this->algorithm = 'HS256';
    }

    public function generateToken($user)
    {
        $payload = [
            'iss' => env('APP_URL'),
            'sub' => $user->id,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDays(7)->timestamp,
            'user_id' => (string) $user->id,
            'email' => $user->email,
            'role' => $user->role
        ];

        return JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function refreshToken($token)
    {
        $payload = $this->validateToken($token);
        if (!$payload) {
            return null;
        }

        // Create new token with extended expiry
        $user = \App\Models\User::find($payload['user_id']);
        if (!$user) {
            return null;
        }
        
        return $this->generateToken($user);
    }
}