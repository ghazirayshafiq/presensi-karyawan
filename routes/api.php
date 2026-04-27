<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendenceController;
use App\Models\Employee;

Route::get('/employees', fn()=>Employee::all());

Route::get('attendence/date',[AttendenceController::class,'getByDate']);
Route::get('attendence/employee/{id}',[AttendenceController::class,'getByEmployee']);

Route::post('attendence/check-in',[AttendenceController::class,'checkIn']);
Route::post('attendence/check-out',[AttendenceController::class,'checkOut']);

Route::apiResource('attendence',AttendenceController::class);