<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Sample Employees
        $employees = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'employee_code' => 'EMP001',
                'full_name' => 'Budi Santoso',
                'position' => 'Manager',
                'department' => 'IT',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@gmail.com',
                'employee_code' => 'EMP002',
                'full_name' => 'Siti Nurhaliza',
                'position' => 'Staff',
                'department' => 'HR',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@gmail.com',
                'employee_code' => 'EMP003',
                'full_name' => 'Ahmad Fauzi',
                'position' => 'Developer',
                'department' => 'IT',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'is_active' => true,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $employeeData['employee_code'],
                'full_name' => $employeeData['full_name'],
                'position' => $employeeData['position'],
                'department' => $employeeData['department'],
                'hire_date' => now()->subMonths(rand(1, 24)),
                'phone' => '081234567' . sprintf('%03d', rand(100, 999)),
                'address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Banjarmasin',
            ]);
        }
    }
}