<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function dashboard()
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();
        $currentMonth = Carbon::now();

        // Today's attendance
        $todayAttendance = $employee->getTodayAttendance();

        // Monthly stats
        $monthlyStats = [
            'total_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth->month)
                ->whereYear('date', $currentMonth->year)
                ->count(),
            'present_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth->month)
                ->whereYear('date', $currentMonth->year)
                ->whereIn('status', ['Hadir', 'Terlambat'])
                ->count(),
            'late_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth->month)
                ->whereYear('date', $currentMonth->year)
                ->where('status', 'Terlambat')
                ->count(),
            'absent_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth->month)
                ->whereYear('date', $currentMonth->year)
                ->where('status', 'Alpa')
                ->count(),
            'leave_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth->month)
                ->whereYear('date', $currentMonth->year)
                ->where('status', 'Izin')
                ->count(),
        ];

        // Recent attendances
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->latest('date')
            ->limit(7)
            ->get();

        // Upcoming holidays
        $upcomingHolidays = Holiday::upcoming()
            ->active()
            ->limit(3)
            ->get();

        // Check if today is workday
        $isWorkday = !$today->isWeekend() && !Holiday::isHoliday($today);

        return view('employee.dashboard', compact(
            'employee', 'todayAttendance', 'monthlyStats', 
            'recentAttendances', 'upcomingHolidays', 'isWorkday'
        ));
    }

    public function attendanceHistory(Request $request)
    {
        $employee = auth()->user()->employee;
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        [$year, $monthNum] = explode('-', $month);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('employee.attendance-history', compact('attendances', 'month'));
    }
}