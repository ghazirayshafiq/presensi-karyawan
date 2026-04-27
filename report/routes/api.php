<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;

Route::get('/report/daily', [ReportController::class, 'daily']);
Route::get('/report/monthly', [ReportController::class, 'monthly']);
Route::get('/report/summary', [ReportController::class, 'summary']);

Route::get('/reports/daily', [ReportController::class, 'daily']);
Route::get('/reports/monthly', [ReportController::class, 'monthly']);
Route::get('/reports/late-stats', [ReportController::class, 'summary']);
Route::get('/reports/late-count/today', [ReportController::class, 'lateCountToday']);