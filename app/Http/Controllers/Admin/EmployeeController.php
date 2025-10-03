<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')
            ->when(request('search'), function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            })
            ->when(request('department'), function ($query, $department) {
                $query->where('department', $department);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(15);

        $departments = Employee::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department');

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:20|unique:employees',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $request->employee_code,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'department' => $request->department,
                'hire_date' => $request->hire_date,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user', 'attendances');
        $attendanceStats = [
            'total_days' => $employee->attendances()->thisMonth()->count(),
            'present_days' => $employee->attendances()->thisMonth()
                ->whereIn('status', ['Hadir', 'Terlambat'])->count(),
            'late_days' => $employee->attendances()->thisMonth()
                ->where('status', 'Terlambat')->count(),
            'absent_days' => $employee->attendances()->thisMonth()
                ->where('status', 'Alpa')->count(),
            'leave_days' => $employee->attendances()->thisMonth()
                ->where('status', 'Izin')->count(),
        ];

        return view('admin.employees.show', compact('employee', 'attendanceStats'));
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users')->ignore($employee->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'full_name' => 'required|string|max:255',
            'employee_code' => ['required', 'string', 'max:20', 
                Rule::unique('employees')->ignore($employee->id)],
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user()->update($userData);

            $employee->update([
                'employee_code' => $request->employee_code,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'department' => $request->department,
                'hire_date' => $request->hire_date,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            $employee->attendances()->delete();
            $employee->user()->delete();
            $employee->delete();
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}