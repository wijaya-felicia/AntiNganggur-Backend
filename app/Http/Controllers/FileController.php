<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|in:profile,resume,company_logo'
        ]);

        $file = $request->file('file');
        $type = $request->type;
        $userId = $request->auth_user->_id; // Get user ID from JWT auth

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs('uploads/' . $type, $filename, 'public');

        // Create file record
        $fileRecord = File::create([
            'user_id' => $userId,
            'type' => $type,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'url' => Storage::url($path)
        ]);

        return $this->successResponse([
            'file' => $fileRecord
        ], 'File uploaded successfully', 201);
    }

    public function show(Request $request, $id)
    {
        $file = File::where('_id', $id)->first();
        if (!$file) {
            return $this->errorResponse('File not found', 404);
        }

        // Check if user has permission to view the file
        if ($file->user_id !== $request->auth_user->_id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($file);
    }

    public function download(Request $request, $id)
    {
        $file = File::where('_id', $id)->first();
        if (!$file) {
            return $this->errorResponse('File not found', 404);
        }

        // Check if user has permission to download the file
        if ($file->user_id !== $request->auth_user->_id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if (!Storage::disk('public')->exists($file->path)) {
            return $this->errorResponse('File not found in storage', 404);
        }

        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    public function destroy(Request $request, $id)
    {
        $file = File::where('_id', $id)->first();
        if (!$file) {
            return $this->errorResponse('File not found', 404);
        }

        // Check if user has permission to delete the file
        if ($file->user_id !== $request->auth_user->_id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        // Delete file from storage
        Storage::delete($file->path);

        // Delete file record
        $file->delete();

        return $this->successResponse(null, 'File deleted successfully');
    }

    public function getImage($type, $filename)
    {
        $fileData = File::where('type', $type)
            ->where('filename', $filename)
            ->first();

        if (!$fileData) {
            return $this->errorResponse('Image not found', 404);
        }

        $path = $fileData->path;

        if (!Storage::disk('public')->exists($path)) {
            return $this->errorResponse('Image file not found in storage', 404);
        }

        return Storage::disk('public')->response($path);
    }

    public function deleteImage($type, $filename)
    {
        $fileData = File::where('type', $type)
            ->where('filename', $filename)
            ->first();

        if (!$fileData) {
            return $this->errorResponse('Image not found', 404);
        }

        // Check if user has permission to delete the file
        if ($fileData->user_id !== $request->auth_user->_id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $path = $fileData->path;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Delete file metadata from MongoDB
        $fileData->delete();

        return $this->successResponse(null, 'Image deleted successfully');
    }

    protected function successResponse($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    protected function errorResponse($message, $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
