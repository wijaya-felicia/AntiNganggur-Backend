<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\Laravel\Eloquent\Model;

class EmployerController extends Controller
{
    public function index()
    {
        $employers = Employer::all();
        return response()->json($employers, 200);
    }

    public function getEmployer($id)
    {
        $employer = Employer::where('_id', $id)->first();
        if (!$employer) {
            return response()->json(['message' => 'Employer not found'], 404);
        }
        return response()->json($employer, 200);
    }

    public function updateEmployer(Request $request, $id)
    {
        $employer = Employer::where('_id', $id)->first();
        if (!$employer) {
            return response()->json(['message' => 'Employer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id . ',_id',
            'phone' => 'sometimes|string',
            'npwp' => 'sometimes|string',
            'address' => 'sometimes|string',
            'deed_of_establishment' => 'sometimes|string',
            'NIB' => 'sometimes|string',
            'website' => 'nullable|string|url',
            'social' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        $employer->update($updateData);

        return response()->json([
            'message' => 'Employer updated successfully',
            'employer' => $employer
        ], 200);
    }

    public function deleteEmployer($id)
    {
        $employer = Employer::where('_id', $id)->first();
        if (!$employer) {
            return response()->json(['message' => 'Employer not found'], 404);
        }

        $employer->delete();

        return response()->json([
            'message' => 'Employer deleted successfully'
        ], 200);
    }
}
