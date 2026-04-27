<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/employees/verify/{id}', [EmployeeController::class, 'verify']);

Route::middleware('auth:api')->prefix('employees')->group(function () {
	Route::get('/', [EmployeeController::class, 'index']);
	Route::get('/{employee}', [EmployeeController::class, 'show']);

	Route::middleware('admin')->group(function () {
		Route::post('/', [EmployeeController::class, 'store']);
		Route::put('/{employee}', [EmployeeController::class, 'update']);
		Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
	});
});
