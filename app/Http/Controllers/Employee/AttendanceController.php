<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if today is workday
        if ($today->isWeekend()) {
            return back()->with('error', 'Tidak bisa absen di hari libur.');
        }

        if (Holiday::isHoliday($today)) {
            return back()->with('error', 'Hari ini adalah hari libur.');
        }

        // Get or create today's attendance record
        $attendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today,
        ], [
            'status' => 'Alpa',
        ]);

        // Check if already checked in
        if ($attendance->time_in) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Perform check in
        $attendance->checkIn();

        $message = $attendance->is_late 
            ? "Absen masuk berhasil! Anda terlambat {$attendance->late_minutes} menit."
            : 'Absen masuk berhasil!';

        return back()->with('success', $message);
    }

    public function checkOut(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if (!$attendance->time_in) {
            return back()->with('error', 'Anda harus absen masuk terlebih dahulu.');
        }

        if ($attendance->time_out) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $attendance->checkOut();

        return back()->with('success', 'Absen pulang berhasil!');
    }

    public function requestLeave(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $employee = auth()->user()->employee;
        $today = Carbon::today();

        // Check if today is workday
        if ($today->isWeekend()) {
            return back()->with('error', 'Tidak bisa mengajukan izin di hari libur.');
        }

        if (Holiday::isHoliday($today)) {
            return back()->with('error', 'Hari ini adalah hari libur.');
        }

        // Get or create today's attendance record
        $attendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today,
        ], [
            'status' => 'Alpa',
        ]);

        // Check if already has attendance record for today
        if ($attendance->time_in || $attendance->status !== 'Alpa') {
            return back()->with('error', 'Anda sudah memiliki record absensi untuk hari ini.');
        }

        $attendance->setPermission($request->reason);

        return back()->with('success', 'Permohonan izin berhasil diajukan.');
    }
}