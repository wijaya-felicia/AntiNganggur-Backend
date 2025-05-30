<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $jwtService;
    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function registerEmployee(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'hard_skills' => 'nullable|array',
            'hard_skills.*' => 'string',
            'soft_skills' => 'nullable|array',
            'soft_skills.*' => 'string',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'education' => $request->education,
            'experience' => $request->experience,
            'hard_skills' => $request->hard_skills ?? [],
            'soft_skills' => $request->soft_skills ?? [],
        ]);

        return $this->successResponse([
            'employee' => $employee
        ], 'Employee registered successfully.', 201);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials.', 401, null);
        }
        $token = $this->jwtService->generateToken($user);
        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Login successful.');
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return $this->errorResponse('Token not provided.', 401);
        }
        $newToken = $this->jwtService->refreshToken($token);
        if (!$newToken) {
            return $this->errorResponse('Invalid or expired token.', 401);
        }
        return $this->successResponse([
            'token' => $token
        ], 'Token refreshed successfully.');
    }
}
