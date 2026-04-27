<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $employees = [1, 2, 3, 4, 5];

        // tanggal mulai
        $startDate = Carbon::create(2026, 4, 1);
        $endDate   = Carbon::create(2026, 4, 30);

        while ($startDate <= $endDate) {

            foreach ($employees as $employeeId) {

                // random kondisi
                $random = rand(1, 100);

                if ($random <= 70) {
                    // HADIR NORMAL
                    $checkIn = '07:' . rand(50, 59) . ':00';
                    $status = 'hadir';
                } elseif ($random <= 90) {
                    // TERLAMBAT
                    $checkIn = '08:' . rand(1, 30) . ':00';
                    $status = 'terlambat';
                } else {
                    // ALPHA
                    Attendance::create([
                        'employee_id' => $employeeId,
                        'date' => $startDate->toDateString(),
                        'check_in' => null,
                        'check_out' => null,
                        'status' => 'alpha'
                    ]);
                    continue;
                }

                Attendance::create([
                    'employee_id' => $employeeId,
                    'date' => $startDate->toDateString(),
                    'check_in' => $checkIn,
                    'check_out' => '17:' . rand(0, 20) . ':00',
                    'status' => $status
                ]);
            }

            $startDate->addDay();
        }
    }
}