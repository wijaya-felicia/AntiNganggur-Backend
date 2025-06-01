<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\Laravel\Eloquent\Model;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::all();
        return response()->json($applications, 200);
    }

    public function getApplication($id)
    {
        $application = Application::where('_id', $id)->first();
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        return response()->json($application, 200);
    }

    public function createApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|string',
            'job_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify employee exists
        $employee = Employee::where('_id', $request->employee_ids)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Verify job exists
        $job = Job::where('_id', $request->job_id)->first();
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Check if application already exists
        $existingApplication = Application::where('employee_ids', $request->employee_ids)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'Application already exists'], 409);
        }

        $application = Application::create([
            'employee_ids' => $request->employee_ids,
            'job_id' => $request->job_id
        ]);

        // Add application to employee's application_ids
        $employee->push('application_ids', $application->_id);

        // Add application to job's application_ids
        $job->push('application_ids', $application->_id);

        return response()->json([
            'message' => 'Application created successfully',
            'application' => $application
        ], 201);
    }

    public function updateApplication(Request $request, $id)
    {
        $application = Application::where('_id', $id)->first();
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_ids' => 'sometimes|string',
            'job_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If employee_ids is being updated, verify new employee exists
        if ($request->has('employee_ids')) {
            $employee = Employee::where('_id', $request->employee_ids)->first();
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }
        }

        // If job_id is being updated, verify new job exists
        if ($request->has('job_id')) {
            $job = Job::where('_id', $request->job_id)->first();
            if (!$job) {
                return response()->json(['message' => 'Job not found'], 404);
            }
        }

        $updateData = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        $application->update($updateData);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application
        ], 200);
    }

    public function deleteApplication($id)
    {
        $application = Application::where('_id', $id)->first();
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        // Remove application from employee's application_ids
        $employee = Employee::where('_id', $application->employee_ids)->first();
        if ($employee) {
            $employee->pull('application_ids', $application->_id);
        }

        // Remove application from job's application_ids
        $job = Job::where('_id', $application->job_id)->first();
        if ($job) {
            $job->pull('application_ids', $application->_id);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ], 200);
    }
}
