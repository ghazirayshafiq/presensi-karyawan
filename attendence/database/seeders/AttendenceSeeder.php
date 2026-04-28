<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendence;
use Carbon\Carbon;

class AttendenceSeeder extends Seeder
{
    public function run()
    {
        $employees = [1,2,3,4,5];

        // 5 hari = total 25 data
        for ($i = 1; $i <= 5; $i++) {
            foreach ($employees as $emp) {

                $random = rand(0,2);

                if ($random == 0) {
                    // HADIR
                    Attendence::create([
                        'employee_id' => $emp,
                        'date' => Carbon::create(2026,4,$i),
                        'check_in' => '07:55:00',
                        'check_out' => '17:00:00',
                        'status' => 'hadir'
                    ]);
                } elseif ($random == 1) {
                    // TERLAMBAT
                    Attendence::create([
                        'employee_id' => $emp,
                        'date' => Carbon::create(2026,4,$i),
                        'check_in' => '08:20:00',
                        'check_out' => '17:05:00',
                        'status' => 'terlambat'
                    ]);
                } else {
                    // ALPHA
                    Attendence::create([
                        'employee_id' => $emp,
                        'date' => Carbon::create(2026,4,$i),
                        'check_in' => null,
                        'check_out' => null,
                        'status' => 'alpha'
                    ]);
                }
            }
        }
    }
}