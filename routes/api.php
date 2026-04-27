<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/employees', fn() => Employee::all());

Route::get('attendance/date', [AttendanceController::class, 'getByDate']);
Route::get('attendance/employee/{employee_id}', [AttendanceController::class, 'getByEmployee']);
Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);

Route::apiResource('attendance', AttendanceController::class);