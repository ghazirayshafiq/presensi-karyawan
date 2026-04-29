<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'name'     => 'Ilham Hilmi',
            'position' => 'Backend Developer',
            'email'    => 'ilham@test.com',
            'password' => Hash::make('12345678'),
        ]);

        Employee::create([
            'name'     => 'Rizky Pratama',
            'position' => 'Frontend Developer',
            'email'    => 'rizky@test.com',
            'password' => Hash::make('12345678'),
        ]);

        Employee::create([
            'name'     => 'Siti Nurhaliza',
            'position' => 'HR Staff',
            'email'    => 'siti@test.com',
            'password' => Hash::make('12345678'),
        ]);

        Employee::create([
            'name'     => 'Andi Saputra',
            'position' => 'UI/UX Designer',
            'email'    => 'andi@test.com',
            'password' => Hash::make('12345678'),
        ]);

        Employee::create([
            'name'     => 'Dewi Lestari',
            'position' => 'Project Manager',
            'email'    => 'dewi@test.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}