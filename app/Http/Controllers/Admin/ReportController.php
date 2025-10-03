<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $department = $request->get('department');
        $employee_id = $request->get('employee_id');

        $query = Attendance::with(['employee.user'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        // Summary statistics
        $summary = [
            'total_records' => $query->count(),
            'present' => $query->clone()->whereIn('status', ['Hadir', 'Terlambat'])->count(),
            'late' => $query->clone()->where('status', 'Terlambat')->count(),
            'absent' => $query->clone()->where('status', 'Alpa')->count(),
            'leave' => $query->clone()->where('status', 'Izin')->count(),
        ];

        $departments = Employee::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department');

        $employees = Employee::with('user')
            ->when($department, function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->get();

        return view('admin.reports.index', compact(
            'attendances', 'summary', 'departments', 'employees',
            'startDate', 'endDate', 'department', 'employee_id'
        ));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'department', 'employee_id']);
        
        return Excel::download(
            new AttendanceExport($filters), 
            'laporan-absensi-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $department = $request->get('department');
        $employee_id = $request->get('employee_id');

        $query = Attendance::with(['employee.user'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('attendances', 'startDate', 'endDate'));
        
        return $pdf->download('laporan-absensi-' . date('Y-m-d') . '.pdf');
    }
}