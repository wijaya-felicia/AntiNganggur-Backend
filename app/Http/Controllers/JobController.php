<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\Laravel\Eloquent\Model;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::all();
        return response()->json($jobs, 200);
    }

    public function getJob($id)
    {
        $job = Job::where('_id', $id)->first();
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        return response()->json($job, 200);
    }

    public function createJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employer_id' => 'required|string',
            'job_role' => 'required|string',
            'job_desc' => 'required|string',
            'req_skills' => 'required|array',
            'salary' => 'required|string',
            'employment_type' => 'required|string',
            'location' => 'required|string',
            'interview_start' => 'required|array',
            'interview_end' => 'required|array',
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify employer exists
        $employer = Employer::where('_id', $request->employer_id)->first();
        if (!$employer) {
            return response()->json(['message' => 'Employer not found'], 404);
        }

        $job = Job::create([
            'employer_id' => $request->employer_id,
            'application_ids' => [],
            'job_role' => $request->job_role,
            'job_desc' => $request->job_desc,
            'req_skills' => $request->req_skills,
            'salary' => $request->salary,
            'employment_type' => $request->employment_type,
            'location' => $request->location,
            'interview_start' => $request->interview_start,
            'interview_end' => $request->interview_end,
            'status' => $request->status
        ]);

        // Add job to employer's job_ids
        $employer->push('job_ids', $job->_id);

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
        ], 201);
    }

    public function updateJob(Request $request, $id)
    {
        $job = Job::where('_id', $id)->first();
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'job_role' => 'sometimes|string',
            'job_desc' => 'sometimes|string',
            'req_skills' => 'sometimes|array',
            'salary' => 'sometimes|string',
            'employment_type' => 'sometimes|string',
            'location' => 'sometimes|string',
            'interview_start' => 'sometimes|array',
            'interview_end' => 'sometimes|array',
            'status' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        $job->update($updateData);

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job
        ], 200);
    }

    public function deleteJob($id)
    {
        $job = Job::where('_id', $id)->first();
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Remove job from employer's job_ids
        $employer = Employer::where('_id', $job->employer_id)->first();
        if ($employer) {
            $employer->pull('job_ids', $job->_id);
        }

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ], 200);
    }
}
