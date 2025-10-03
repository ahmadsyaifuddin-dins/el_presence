<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'position',
        'department',
        'hire_date',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Helper Methods
    public function getTodayAttendance()
    {
        return $this->attendances()
            ->where('date', Carbon::today())
            ->first();
    }

    public function getAttendanceByDate($date)
    {
        return $this->attendances()
            ->where('date', $date)
            ->first();
    }

    public function hasCheckedInToday()
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->time_in;
    }

    public function hasCheckedOutToday()
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->time_out;
    }

    public function getWorkingDays($startDate, $endDate)
    {
        return $this->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->count();
    }
}