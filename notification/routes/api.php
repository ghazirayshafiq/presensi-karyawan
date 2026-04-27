<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::get('/notifications/employee/{employee_id}', [NotificationController::class, 'showByEmployee']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
