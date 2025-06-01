<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FileController;

Route::post('/employees', [AuthController::class, 'registerEmployee'])->name('employees.register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('jwt.auth')->group(function () {
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name('token.refresh');
});
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::get('/{id}', [EmployeeController::class, 'getEmployee']);
    Route::put('/{id}', [EmployeeController::class, 'updateEmployee']);
    Route::delete('/{id}', [EmployeeController::class, 'deleteEmployee']);
});

Route::prefix('employers')->group(function () {
    Route::get('/', [EmployerController::class, 'index']);
    Route::get('/{id}', [EmployerController::class, 'getEmployer']);
    Route::put('/{id}', [EmployerController::class, 'updateEmployer']);
    Route::delete('/{id}', [EmployerController::class, 'deleteEmployer']);
});

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/{id}', [JobController::class, 'getJob']);
    Route::post('/', [JobController::class, 'createJob']);
    Route::put('/{id}', [JobController::class, 'updateJob']);
    Route::delete('/{id}', [JobController::class, 'deleteJob']);
});

Route::prefix('applications')->group(function () {
    Route::get('/', [ApplicationController::class, 'index']);
    Route::get('/{id}', [ApplicationController::class, 'getApplication']);
    Route::post('/', [ApplicationController::class, 'createApplication']);
    Route::put('/{id}', [ApplicationController::class, 'updateApplication']);
    Route::delete('/{id}', [ApplicationController::class, 'deleteApplication']);
});

// File routes
Route::middleware('jwt.auth')->group(function () {
    Route::post('/files', [FileController::class, 'upload']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::get('/files/{id}/download', [FileController::class, 'download']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::get('/files/{type}/{filename}', [FileController::class, 'getImage']);
    Route::delete('/files/{type}/{filename}', [FileController::class, 'deleteImage']);
    Route::get('/files/user/{userId}', [FileController::class, 'getUserImages']);
});
