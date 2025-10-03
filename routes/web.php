<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('employee.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/generate-attendance', [AdminController::class, 'generateAttendanceRecords'])
        ->name('generate.attendance');
    
    // Employee Management
    Route::resource('employees', EmployeeController::class);
    
    // Attendance Management
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::patch('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])
        ->name('attendance.update');
    Route::post('/attendance/bulk-update', [AdminAttendanceController::class, 'bulkUpdate'])
        ->name('attendance.bulk-update');
    
    // Holiday Management
    Route::resource('holidays', HolidayController::class);
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
});

// Employee Routes
Route::middleware(['auth', 'role:karyawan'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance-history', [EmployeeDashboardController::class, 'attendanceHistory'])
        ->name('attendance.history');
    
    // Attendance Actions
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/request-leave', [AttendanceController::class, 'requestLeave'])->name('attendance.leave');
});

require __DIR__.'/auth.php';