<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $authEmployee = $request->user('api');
        $department = $request->query('department');

        if ($authEmployee->role === 'admin') {
            $employeesQuery = Employee::query();

            if ($department) {
                $employeesQuery->where('department', $department);
            }

            $employees = $employeesQuery->get();
        } else {
            $employeesQuery = Employee::query()->whereKey($authEmployee->id);

            if ($department) {
                $employeesQuery->where('department', $department);
            }

            $employees = $employeesQuery->get();
        }

        return $this->success('Data karyawan berhasil diambil.', $employees);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'karyawan'])],
            'department' => ['required', 'string', 'max:255'],
        ]);

        $employee = Employee::create($validated);

        return $this->success('Data karyawan berhasil ditambahkan.', $employee, 201);
    }

    public function show(Request $request, Employee $employee): JsonResponse
    {
        $authEmployee = $request->user('api');

        if ($authEmployee->role !== 'admin' && $authEmployee->id !== $employee->id) {
            return $this->error('Anda tidak memiliki akses ke data ini.', null, 403);
        }

        return $this->success('Detail karyawan berhasil diambil.', $employee);
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', Rule::in(['admin', 'karyawan'])],
            'department' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        if (array_key_exists('password', $validated) && empty($validated['password'])) {
            unset($validated['password']);
        }

        $employee->update($validated);

        return $this->success('Data karyawan berhasil diperbarui.', $employee);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return $this->success('Data karyawan berhasil dihapus.', null);
    }

    public function verify(int $id): JsonResponse
    {
        $employee = Employee::query()->find($id);

        if (! $employee) {
            return $this->error('Data karyawan tidak ditemukan.', null, 404);
        }

        return $this->success('Data verifikasi karyawan berhasil diambil.', [
            'id' => $employee->id,
            'name' => $employee->name,
            'role' => $employee->role,
            'department' => $employee->department,
        ]);
    }
}
