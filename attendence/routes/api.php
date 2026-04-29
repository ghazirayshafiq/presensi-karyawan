<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendenceController;
use App\Http\Controllers\AuthController; 
use App\Models\Employee;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('attendence/check-in', [AttendenceController::class, 'checkIn']);  // ← pindah ke sini
    Route::post('attendence/check-out', [AttendenceController::class, 'checkOut']);
    
    Route::get('/employees', fn()=>Employee::all());
    Route::get('attendence/date', [AttendenceController::class, 'getByDate']);
    Route::get('attendence/employee/{id}', [AttendenceController::class, 'getByEmployee']);
    Route::apiResource('attendence', AttendenceController::class);
});