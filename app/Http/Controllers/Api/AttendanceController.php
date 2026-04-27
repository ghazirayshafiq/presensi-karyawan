<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Attendance::with('employee')->get()
        ]);
    }

    public function show($id)
    {
        $data = Attendance::with('employee')->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getByDate(Request $request)
    {
        $date = $request->date;

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'date wajib diisi'
            ], 400);
        }

        $data = Attendance::with('employee')
            ->where('date', $date)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getByEmployee($employee_id)
    {
        $data = Attendance::with('employee')
            ->where('employee_id', $employee_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function checkIn(Request $request)
    {
        $employeeId = $request->employee_id;

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah check-in hari ini'
            ], 400);
        }

        $status = $now->format('H:i:s') > '08:00:00' ? 'terlambat' : 'hadir';

        if (!$attendance) {
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'check_in' => $now->format('H:i:s'),
                'status' => $status
            ]);
        } else {
            $attendance->update([
                'check_in' => $now->format('H:i:s'),
                'status' => $status
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil',
            'data' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $employeeId = $request->employee_id;

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Belum check-in'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah check-out'
            ], 400);
        }

        $attendance->update([
            'check_out' => $now->format('H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil',
            'data' => $attendance
        ]);
    }
}