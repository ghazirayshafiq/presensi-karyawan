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

        $data = Attendence::where('employee_id',$request->employee_id)
            ->where('date',$today)
            ->first();

        if ($data && $data->check_in) {
            return response()->json(['message'=>'sudah check in'],400);
        }

        $status = $now->format('H:i:s') > '08:00:00' ? 'terlambat' : 'hadir';

        return Attendence::create([
            'employee_id'=>$request->employee_id,
            'date'=>$today,
            'check_in'=>$now->format('H:i:s'),
            'status'=>$status
        ]);
    }

    public function checkOut(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $data = Attendence::where('employee_id',$request->employee_id)
            ->where('date',$today)
            ->first();

        if (!$data) {
            return response()->json(['message'=>'belum check in'],400);
        }

        if ($data->check_out) {
            return response()->json(['message'=>'sudah check out'],400);
        }

        $data->update([
            'check_out'=>now()->format('H:i:s')
        ]);

        return $data;
        
    }
}