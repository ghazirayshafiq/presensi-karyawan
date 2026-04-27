<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function getEmployees()
    {
        $response = Http::get(env('EMPLOYEE_SERVICE_URL') . '/employees');
        return $response->successful() ? $response->json()['data'] : [];
    }


    private function getAttendances()
    {
        $response = Http::get(env('ATTENDANCE_SERVICE_URL') . '/attendance');
        return $response->successful() ? $response->json()['data'] : [];
    }


    public function summary()
    {
        $employees = $this->getEmployees();
        $attendances = $this->getAttendances();

        $report = [];

        foreach ($employees as $employee) {
           
            $employeeAttendances = array_filter($attendances, function ($att) use ($employee) {
                return $att['employee_id'] == $employee['id'];
            });

            $totalHadir = 0;
            $totalTelat = 0;
            $totalAlpha = 0;

            foreach ($employeeAttendances as $att) {
                if ($att['status'] === 'on_time') {
                    $totalHadir++;
                } elseif ($att['status'] === 'late') {
                    $totalHadir++; 
                    $totalTelat++;
                } elseif ($att['status'] === 'absent') {
                    $totalAlpha++;
                }
            }

          
            $report[] = [
                'employee' => $employee['name'],
                'total_hadir' => $totalHadir,
                'total_telat' => $totalTelat,
                'total_alpha' => $totalAlpha
            ];
        }

        return response()->json([
            'message' => 'Summary Report Generated',
            'data' => $report
        ], 200);
    }

    public function daily()
    {
        $employees = collect($this->getEmployees());
        $attendances = $this->getAttendances();
        
        $todayStr = Carbon::today()->toDateString();
        
        $todayAttendances = array_filter($attendances, function ($att) use ($todayStr) {
            return str_starts_with($att['checkin_time'], $todayStr);
        });

        $report = [];

        foreach ($todayAttendances as $att) {
            $emp = $employees->firstWhere('id', $att['employee_id']);
            
            $report[] = [
                'employee_name' => $emp ? $emp['name'] : 'Unknown',
                'checkin_time' => $att['checkin_time'],
                'checkout_time' => $att['checkout_time'] ?? '-',
                'status' => $att['status']
            ];
        }

        return response()->json([
            'message' => 'Daily Report for ' . $todayStr,
            'data' => $report
        ], 200);
    }

    public function monthly()
    {
        $attendances = $this->getAttendances();
        
        $groupedByMonth = [];

        foreach ($attendances as $att) {
            if (!$att['checkin_time']) continue;

            $month = Carbon::parse($att['checkin_time'])->format('Y-m');
            
            if (!isset($groupedByMonth[$month])) {
                $groupedByMonth[$month] = ['present' => 0, 'late' => 0];
            }

            if ($att['status'] === 'on_time' || $att['status'] === 'late') {
                $groupedByMonth[$month]['present']++;
            }
            if ($att['status'] === 'late') {
                $groupedByMonth[$month]['late']++;
            }
        }

        $report = [];
        foreach ($groupedByMonth as $month => $stats) {
            $report[] = [
                'month' => Carbon::parse($month)->translatedFormat('F Y'),
                'present' => $stats['present'],
                'late' => $stats['late']
            ];
        }

        return response()->json([
            'message' => 'Monthly Aggregated Report',
            'data' => $report
        ], 200);
    }
    
    public function lateCountToday()
    {
        $attendances = $this->getAttendances();
        $todayStr = Carbon::today()->toDateString();
        
        $lateToday = count(array_filter($attendances, function ($att) use ($todayStr) {
            return str_starts_with($att['checkin_time'], $todayStr) && $att['status'] === 'late';
        }));

        return response()->json(['data' => $lateToday], 200);
    }
}