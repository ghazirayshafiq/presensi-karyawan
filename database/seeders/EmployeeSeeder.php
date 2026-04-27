<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'name' => 'Ilham Hilmi',
            'position' => 'Backend Developer'
        ]);

        Employee::create([
            'name' => 'Rizky Pratama',
            'position' => 'Frontend Developer'
        ]);

        Employee::create([
            'name' => 'Siti Nurhaliza',
            'position' => 'HR Staff'
        ]);

        Employee::create([
            'name' => 'Andi Saputra',
            'position' => 'UI/UX Designer'
        ]);

        Employee::create([
            'name' => 'Dewi Lestari',
            'position' => 'Project Manager'
        ]);
    }
}