<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendence;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendenceController extends Controller
{
    public function index()
    {
        return Attendence::with('employee')->get();
    }

    public function show($id)
    {
        return Attendence::with('employee')->find($id);
    }

    public function getByDate(Request $request)
    {
        return Attendence::where('date',$request->date)->get();
    }

    public function getByEmployee($id)
    {
        return Attendence::where('employee_id',$id)->get();
    }

    public function checkIn(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        
        $employeeId = auth('api')->id() ?: auth('api')->payload()->get('sub'); 

        if (!$employeeId) {
            return response()->json(['message' => 'User tidak terdeteksi, silakan login ulang'], 401);
        }

        $data = Attendence::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if ($data && $data->check_in) {
            return response()->json(['message' => 'Anda sudah melakukan check-in hari ini'], 400);
        }

        $status = $now->format('H:i:s') > '08:00:00' ? 'terlambat' : 'hadir';

        $attendance = Attendence::create([
            'employee_id' => $employeeId,
            'date'        => $today,
            'check_in'    => $now->format('H:i:s'),
            'status'      => $status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'data'    => $attendance
        ], 201);
    }

    public function checkOut(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $employeeId = auth('api')->id() ?: auth('api')->payload()->get('sub'); 

        $data = Attendence::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Anda belum check-in hari ini'], 400);
        }

        if ($data->check_out) {
            return response()->json(['message' => 'Anda sudah check-out hari ini'], 400);
        }

        $data->update([
            'check_out' => now()->format('H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'data'    => $data
        ], 200);
    }
}