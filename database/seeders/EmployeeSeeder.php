<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            ['name' => 'Ilham Hilmi', 'position' => 'Backend Developer'],
            ['name' => 'Rizky Pratama', 'position' => 'Frontend Developer'],
            ['name' => 'Siti Nurhaliza', 'position' => 'HR Staff'],
            ['name' => 'Andi Saputra', 'position' => 'UI/UX Designer'],
            ['name' => 'Dewi Lestari', 'position' => 'Project Manager'],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }
    }
}