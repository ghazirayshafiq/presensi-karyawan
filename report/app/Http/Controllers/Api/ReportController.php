<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $attendenceUrl = 'http://127.0.0.1:8002/api';

    public function daily(Request $request)
    {
        $date = $request->date ?? Carbon::today()->toDateString();

        try {
            $response = Http::timeout(10)->get("{$this->attendenceUrl}/attendence/date", [
                'date' => $date
            ]);

            $result = $response->json();
            $data = is_array($result) ? ($result['data'] ?? $result) : [];

            return response()->json([
                'success' => true,
                'date'    => $date,
                'total'   => count($data),
                'data'    => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function monthly(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');

        try {
            $response = Http::timeout(10)->get("{$this->attendenceUrl}/attendence");
            $result = $response->json();
            $all = collect(is_array($result) ? ($result['data'] ?? $result) : []);

            $filtered = $all->filter(fn($item) =>
                isset($item['date']) && str_starts_with($item['date'], $month)
            );

            $grouped = $filtered->groupBy('employee_id')->map(function ($records) {
                $first = $records->first();
                return [
                    'employee'        => $first['employee']['name'] ?? '-',
                    'position'        => $first['employee']['position'] ?? '-',
                    'total_hadir'     => $records->where('status', 'hadir')->count(),
                    'total_terlambat' => $records->where('status', 'terlambat')->count(),
                    'total_hari'      => $records->count(),
                    'detail'          => $records->values()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'month'   => $month,
                'total'   => $grouped->count(),
                'data'    => $grouped
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function summary()
    {
        try {
            $response = Http::timeout(10)->get("{$this->attendenceUrl}/attendence");
            $result = $response->json();
            $all = collect(is_array($result) ? ($result['data'] ?? $result) : []);

            return response()->json([
                'success'         => true,
                'total_records'   => $all->count(),
                'total_hadir'     => $all->where('status', 'hadir')->count(),
                'total_terlambat' => $all->where('status', 'terlambat')->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function lateStats()
    {
        try {
            $response = Http::timeout(10)->get("{$this->attendenceUrl}/attendence");
            $result = $response->json();
            $all = collect(is_array($result) ? ($result['data'] ?? $result) : []);

            $late = $all->where('status', 'terlambat')
                ->groupBy('employee_id')
                ->map(function ($records) {
                    $first = $records->first();
                    return [
                        'employee'        => $first['employee']['name'] ?? '-',
                        'total_terlambat' => $records->count(),
                    ];
                })->values();

            return response()->json([
                'success' => true,
                'data'    => $late
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function lateCountToday()
    {
        $today = Carbon::today()->toDateString();

        try {
            $response = Http::timeout(10)->get("{$this->attendenceUrl}/attendence/date", [
                'date' => $today
            ]);

            $result = $response->json();
            $data = collect(is_array($result) ? ($result['data'] ?? $result) : []);

            return response()->json([
                'success'    => true,
                'date'       => $today,
                'late_count' => $data->where('status', 'terlambat')->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}