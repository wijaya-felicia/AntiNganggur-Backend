<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use Closure;
use Illuminate\Http\Request;

class JWTMiddleware
{
    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $payload = $this->jwtService->validateToken($token);
        if (!$payload) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user = \App\Models\User::find($payload['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $request->merge(['auth_user' => $user]);
        return $next($request);
    }
}