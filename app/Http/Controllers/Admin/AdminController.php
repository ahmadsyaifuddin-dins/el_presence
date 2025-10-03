<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        
        $stats = [
            'total_employees' => Employee::active()->count(),
            'present_today' => Attendance::today()
                ->whereIn('status', ['Hadir', 'Terlambat'])
                ->count(),
            'absent_today' => Attendance::today()
                ->where('status', 'Alpa')
                ->count(),
            'on_leave_today' => Attendance::today()
                ->where('status', 'Izin')
                ->count(),
            'late_today' => Attendance::today()
                ->where('status', 'Terlambat')
                ->count(),
        ];

        $recent_attendances = Attendance::with('employee.user')
            ->today()
            ->latest('checked_in_at')
            ->limit(10)
            ->get();

        $upcoming_holidays = Holiday::upcoming()
            ->active()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_attendances', 'upcoming_holidays'));
    }

    public function generateAttendanceRecords()
    {
        $today = Carbon::today();
        
        // Skip weekends
        if ($today->isWeekend()) {
            return back()->with('info', 'Hari libur, tidak ada record absensi yang dibuat.');
        }

        // Skip holidays
        if (Holiday::isHoliday($today)) {
            return back()->with('info', 'Hari libur nasional, tidak ada record absensi yang dibuat.');
        }

        $employees = Employee::active()->get();
        $created = 0;

        foreach ($employees as $employee) {
            $existing = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if (!$existing) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'status' => 'Alpa',
                ]);
                $created++;
            }
        }

        return back()->with('success', "Berhasil membuat {$created} record absensi untuk hari ini.");
    }
}