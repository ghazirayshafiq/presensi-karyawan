<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    
    public function run(): void
{
    Employee::create([
        'name'     => 'admin', 
        'email'    => 'admin@test.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'department' => 'IT',       
        'role'    => 'admin'
    ]);

    Employee::factory(50)->create();
}
}
