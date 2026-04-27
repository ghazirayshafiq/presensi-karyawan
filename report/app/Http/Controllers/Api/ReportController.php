<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Method Helper untuk mengambil data Karyawan
    private function getEmployees()
    {
        $response = Http::get(env('EMPLOYEE_SERVICE_URL') . '/employees');
        return $response->successful() ? $response->json()['data'] : [];
    }

    // Method Helper untuk mengambil data Presensi
    private function getAttendances()
    {
        $response = Http::get(env('ATTENDANCE_SERVICE_URL') . '/attendance');
        return $response->successful() ? $response->json()['data'] : [];
    }

    // 1. GET /api/report/summary (Statistik Kehadiran per Karyawan)
    public function summary()
    {
        $employees = $this->getEmployees();
        $attendances = $this->getAttendances();

        $report = [];

        foreach ($employees as $employee) {
            // Filter presensi hanya untuk karyawan ini
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
                    $totalHadir++; // Telat tetap dihitung hadir
                    $totalTelat++;
                } elseif ($att['status'] === 'absent') {
                    $totalAlpha++;
                }
            }

            // Jika status alpha tidak dicatat secara eksplisit di DB, 
            // Anda bisa menggunakan asumsi hari kerja dikurangi total hadir.
            // Contoh asumsi: Hari kerja berjalan ada 22 hari.
            // $totalAlpha = 22 - $totalHadir; 

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

    // 2. GET /api/report/daily (Rekap Hari Ini)
    public function daily()
    {
        $employees = collect($this->getEmployees());
        $attendances = $this->getAttendances();
        
        $todayStr = Carbon::today()->toDateString();
        
        // Ambil absensi yang tanggalnya hari ini saja
        $todayAttendances = array_filter($attendances, function ($att) use ($todayStr) {
            return str_starts_with($att['checkin_time'], $todayStr);
        });

        $report = [];

        foreach ($todayAttendances as $att) {
            // Cari nama karyawan berdasarkan ID
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

    // 3. GET /api/report/monthly (Rekap Bulanan Keseluruhan)
    public function monthly()
    {
        $attendances = $this->getAttendances();
        
        // Mengelompokkan data berdasarkan Bulan (contoh: "2026-04")
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

        // Format ulang menjadi array biasa agar mudah dikonsumsi frontend
        $report = [];
        foreach ($groupedByMonth as $month => $stats) {
            $report[] = [
                'month' => Carbon::parse($month)->translatedFormat('F Y'), // Menjadi "April 2026"
                'present' => $stats['present'],
                'late' => $stats['late']
            ];
        }

        return response()->json([
            'message' => 'Monthly Aggregated Report',
            'data' => $report
        ], 200);
    }
    
    // Opsional: Endpoint tambahan untuk dashboard frontend (Hitung total telat hari ini)
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