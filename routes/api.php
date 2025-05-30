<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::post('/employees', [AuthController::class, 'registerEmployee'])->name('employees.register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('jwt.auth')->group(function () {
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name('token.refresh');
});