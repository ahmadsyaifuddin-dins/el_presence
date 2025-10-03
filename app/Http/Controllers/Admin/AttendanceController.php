<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $department = $request->get('department');
        $status = $request->get('status');

        $attendances = Attendance::with(['employee.user'])
            ->byDate($date)
            ->when($department, function ($query, $department) {
                $query->whereHas('employee', function ($q) use ($department) {
                    $q->where('department', $department);
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->get();

        $departments = Employee::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department');

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->whereIn('status', ['Hadir', 'Terlambat'])->count(),
            'late' => $attendances->where('status', 'Terlambat')->count(),
            'absent' => $attendances->where('status', 'Alpa')->count(),
            'leave' => $attendances->where('status', 'Izin')->count(),
        ];

        return view('admin.attendance.index', compact(
            'attendances', 'departments', 'stats', 'date', 'department', 'status'
        ));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Terlambat,Izin,Alpa,Libur',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendances,id',
            'bulk_status' => 'required|in:Hadir,Terlambat,Izin,Alpa,Libur',
        ]);

        Attendance::whereIn('id', $request->attendance_ids)
            ->update(['status' => $request->bulk_status]);

        return back()->with('success', 'Bulk update berhasil dilakukan.');
    }
}