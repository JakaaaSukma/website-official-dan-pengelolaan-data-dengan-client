<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Mobile App API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no token required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-login', [AuthController::class, 'verifyLogin']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes (requires valid token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/request-change-password-otp', [AuthController::class, 'requestChangePasswordOtp']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('/employees', [\App\Http\Controllers\Api\EmployeeController::class, 'index']);
    Route::get('/clients', [\App\Http\Controllers\Api\ClientController::class, 'index']);
    Route::get('/candidates', [\App\Http\Controllers\Api\CandidateController::class, 'index']);
    Route::get('/contracts', [\App\Http\Controllers\Api\ContractController::class, 'index']);
    Route::get('/contracts/create', [\App\Http\Controllers\Api\ContractController::class, 'create']);
    Route::post('/contracts', [\App\Http\Controllers\Api\ContractController::class, 'store']);
    Route::get('/talents', [\App\Http\Controllers\Api\TalentController::class, 'index']);
    Route::get('/violations', [\App\Http\Controllers\Api\ViolationController::class, 'index']);
    Route::get('/bpjs/accidents', [\App\Http\Controllers\Api\BpjsController::class, 'accidents']);
    Route::get('/bpjs/f2a', [\App\Http\Controllers\Api\BpjsController::class, 'f2a']);
    Route::get('/operations', [\App\Http\Controllers\Api\OperationController::class, 'index']);
    Route::get('/suspensions', [\App\Http\Controllers\Api\SuspensionController::class, 'index']);
    Route::get('/logs', [\App\Http\Controllers\Api\LogController::class, 'index']);
    Route::get('/messages', [\App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::post('/messages/{id}/read', [\App\Http\Controllers\Api\MessageController::class, 'markAsRead']);
});
