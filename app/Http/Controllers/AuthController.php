<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // JWT Service Constructor
    protected $jwtService;
    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    //Use registerEmployee to register a new account for employees
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

    // use registerEmployer to register new accounts for companies/employers
    public function registerEmployer(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'npwp' => 'required|string',
            'address' => 'required|string',
            'deed_of_establishment' => 'nullable|string',
            'NIB' => 'nullable|string',
            'website' => 'nullable|string',
            'social' => 'nullable|string',
        ]);

        $employer = Employer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'npwp' => $request->npwp,
            'address' => $request->address,
            'deed_of_establishment' => $request->deed_of_establishment,
            'NIB' => $request->nib,
            'website' => $request->website,
            'social' => $request->social,
        ]);

        return $this->successResponse([
            'employer' => $employer
        ], 'Employer registered successfully.', 201);
    }

    // Use login for user log in, for both employees and companies/employers
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

    // refresh user token so their session doesn't end
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
