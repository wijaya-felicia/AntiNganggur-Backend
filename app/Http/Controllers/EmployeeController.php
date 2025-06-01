<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\Laravel\Eloquent\Model;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees, 200);
    }

    public function getEmployee($id)
    {
        $employee = Employee::where('_id', $id)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee, 200);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::where('_id', $id)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id . ',_id',
            'phone' => 'sometimes|string',
            'education' => 'sometimes|string',
            'experience' => 'sometimes|string',
            'hard_skills' => 'sometimes|array',
            'soft_skills' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        $employee->update($updateData);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ], 200);
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::where('_id', $id)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ], 200);
    }
}
